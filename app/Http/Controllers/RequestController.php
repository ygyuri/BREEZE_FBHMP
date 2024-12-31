<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    /**
     * Display a listing of the requests with pagination and optional filtering.
     *
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(HttpRequest $request)
    {
        // Log the incoming request for filtering and pagination
        Log::info('Fetching requests', ['request' => $request->all()]);

        // Validate optional filters and pagination
        $validated = $request->validate([
            'foodbank_id' => 'nullable|exists:users,id|role:foodbank', // Ensure foodbank_id exists in the users table and is a foodbank
            'type' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer',
            'page' => 'nullable|integer|min:1', // Pagination parameter
            'per_page' => 'nullable|integer|min:1|max:100', // Pagination size
        ]);

        try {
            // Fetch requests with optional filtering and pagination
            $requests = Request::when($validated['foodbank_id'] ?? null, function ($query, $foodbankId) {
                    return $query->whereHas('foodbank', function($query) use ($foodbankId) {
                        return $query->where('users.id', $foodbankId); // Check foodbank's user relation
                    });
                })
                ->when($validated['type'] ?? null, function ($query, $type) {
                    return $query->where('type', 'like', '%' . $type . '%');
                })
                ->when($validated['quantity'] ?? null, function ($query, $quantity) {
                    return $query->where('quantity', $quantity);
                })
                ->paginate($validated['per_page'] ?? 10); // Default pagination size is 10

            Log::info('Requests fetched successfully', ['requests' => $requests]);

            return response()->json($requests, 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch requests', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch requests', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created request in the database.
     *
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(HttpRequest $request)
    {
        // Log the incoming request data
        Log::info('Creating request', ['request' => $request->all()]);

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'foodbank_id' => 'required|exists:users,id|role:foodbank', // foodbank_id must exist and be a foodbank
            'type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed while creating request', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Sanitize and create the request using validated data
            $req = Request::create([
                'foodbank_id' => $request->input('foodbank_id'),
                'type' => e($request->input('type')), // Sanitize the type field
                'quantity' => $request->input('quantity'),
            ]);

            Log::info('Request created successfully', ['request' => $req]);

            return response()->json(['message' => 'Request created successfully', 'data' => $req], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create request', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create request', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified request.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        Log::info("Fetching request with ID: $id");

        try {
            $req = Request::findOrFail($id);

            Log::info('Request fetched successfully', ['request' => $req]);

            return response()->json($req, 200);
        } catch (\Exception $e) {
            Log::error("Failed to fetch request with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Request not found', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Update the specified request in the database.
     *
     * @param HttpRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(HttpRequest $request, $id)
    {
        // Log the incoming request data
        Log::info("Updating request with ID: $id", ['request' => $request->all()]);

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'foodbank_id' => 'sometimes|exists:users,id|role:foodbank',
            'type' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed while updating request with ID: $id", ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Fetch request and update
            $req = Request::findOrFail($id);
            $req->update($request->only(['foodbank_id', 'type', 'quantity']));

            Log::info('Request updated successfully', ['request' => $req]);

            return response()->json(['message' => 'Request updated successfully', 'data' => $req], 200);
        } catch (\Exception $e) {
            Log::error("Failed to update request with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update request', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified request from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Log::info("Deleting request with ID: $id");

        try {
            // Soft delete the request
            $req = Request::findOrFail($id);
            $req->delete();

            Log::info('Request deleted successfully', ['request_id' => $id]);

            return response()->json(['message' => 'Request deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Failed to delete request with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete request', 'message' => $e->getMessage()], 500);
        }
    }
}