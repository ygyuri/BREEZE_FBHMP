<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Donation;
//use App\Notifications\DonationAssigned;


class RequestController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }
    // /**
    //  * Display a listing of the requests with pagination and optional filtering.
    //  *
    //  * @param HttpRequest $request
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function index(HttpRequest $request)
    // {
    //     // Log the incoming request for filtering and pagination
    //     Log::info('Fetching requests', ['request' => $request->all()]);

    //     // Validate optional filters and pagination
    //     $validated = $request->validate([
    //         'foodbank_id' => 'nullable|exists:users,id|role:foodbank', // Ensure foodbank_id exists in the users table and is a foodbank
    //         'type' => 'nullable|string|max:255',
    //         'quantity' => 'nullable|integer',
    //         'page' => 'nullable|integer|min:1', // Pagination parameter
    //         'per_page' => 'nullable|integer|min:1|max:100', // Pagination size
    //     ]);

    //     try {
    //         // Fetch requests with optional filtering and pagination
    //         $requests = Request::when($validated['foodbank_id'] ?? null, function ($query, $foodbankId) {
    //                 return $query->whereHas('foodbank', function($query) use ($foodbankId) {
    //                     return $query->where('users.id', $foodbankId); // Check foodbank's user relation
    //                 });
    //             })
    //             ->when($validated['type'] ?? null, function ($query, $type) {
    //                 return $query->where('type', 'like', '%' . $type . '%');
    //             })
    //             ->when($validated['quantity'] ?? null, function ($query, $quantity) {
    //                 return $query->where('quantity', $quantity);
    //             })
    //             ->paginate($validated['per_page'] ?? 10); // Default pagination size is 10

    //         Log::info('Requests fetched successfully', ['requests' => $requests]);

    //         return response()->json($requests, 200);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to fetch requests', ['error' => $e->getMessage()]);
    //         return response()->json(['error' => 'Failed to fetch requests', 'message' => $e->getMessage()], 500);
    //     }
    // }

    /**
     * Display a listing of the requests with pagination and optional filtering.
     *
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(HttpRequest $request)
    {
        Log::info('Fetching requests', ['request' => $request->all()]);

        $validated = $request->validate([
            'type' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        try {
            $requests = Request::where(function ($query) {
                $query->where('foodbank_id', auth()->id()) // Foodbank owner
                    ->orWhereHas('foodbank', function ($subQuery) {
                        $subQuery->where('role', 'admin'); // Admin role
                    });
            })
            ->when($request->type, fn($query, $type) => $query->where('type', $type))
            ->when($request->quantity, fn($query, $quantity) => $query->where('quantity', $quantity))
            ->paginate($validated['per_page'] ?? 10);

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




        // Check authorization: Only foodbanks or admins can create requests
        if (!auth()->user()->hasRole('foodbank') && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the incoming request
        $validatedData = $request->validate([
            'type' => 'required|string|max:255', // e.g., food, clothes
            'quantity' => 'required|integer|min:1', // Must be a positive integer
        ]);

        try {
            // Sanitize and associate the request with the authenticated foodbank or provided foodbank_id if admin
            $requestData = array_merge($validatedData, [
                'foodbank_id' => auth()->user()->hasRole('admin') ? $request->input('foodbank_id') : auth()->id()
            ]);

            // Create the request
            $req = Request::create($requestData);

            // Log the successful creation
            Log::info('Request created successfully', ['request' => $req]);

            return response()->json(['message' => 'Request created successfully', 'data' => $req], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create request', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create request', 'message' => $e->getMessage()], 500);
        }
    }


    // /**
    //  * Display the specified request.
    //  *
    //  * @param int $id
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function show($id)
    // {
    //     Log::info("Fetching request with ID: $id");

    //     try {
    //         $req = Request::findOrFail($id);

    //         Log::info('Request fetched successfully', ['request' => $req]);

    //         return response()->json($req, 200);
    //     } catch (\Exception $e) {
    //         Log::error("Failed to fetch request with ID: $id", ['error' => $e->getMessage()]);
    //         return response()->json(['error' => 'Request not found', 'message' => $e->getMessage()], 404);
    //     }
    // }

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
            // Fetch the request, ensuring it belongs to the authenticated foodbank or is accessed by an admin
            $req = Request::where('id', $id)
                ->where(function ($query) {
                    $query->where('foodbank_id', auth()->id()) // Foodbank owner
                        ->orWhereHas('foodbank', function ($subQuery) {
                            $subQuery->where('role', 'admin'); // Admin role
                        });
                })
                ->firstOrFail();

            Log::info('Request fetched successfully', ['request' => $req]);

            return response()->json(['data' => $req], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Request not found with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Request not found'], 404);
        } catch (\Exception $e) {
            Log::error("Failed to fetch request with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
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

        try {
            // Fetch the request to update
            $req = Request::findOrFail($id);

            // Check authorization: Only admins or the associated foodbank can update the request
            if (!auth()->user()->hasRole('admin') && auth()->id() !== $req->foodbank_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Validate the incoming request
            $validatedData = $request->validate([
                'type' => 'sometimes|string|max:255',
                'quantity' => 'sometimes|integer|min:1',
            ]);

            // Sanitize and update the request
            $req->update($validatedData);

            // Log the successful update
            Log::info('Request updated successfully', ['request' => $req]);

            return response()->json(['message' => 'Request updated successfully', 'data' => $req], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Request not found with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Request not found'], 404);
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

    public function assignDonationToRequest($requestId, $donationId)
    {
        try {
            // Fetch the request and donation, ensuring they belong to the authenticated foodbank
            $request = Request::where('id', $requestId)
                ->where('foodbank_id', auth()->id()) // Check if the request belongs to the authenticated foodbank
                ->firstOrFail();

            $donation = Donation::findOrFail($donationId); // Fetch the donation

            // Validate that the donation type and quantity meet the request's requirements
            if ($donation->type !== $request->type || $donation->quantity < $request->quantity) {
                return response()->json(['error' => 'Donation does not match request criteria'], 422);
            }

            // Log the assignment process
            Log::info('Assigning donation to request', [
                'request_id' => $requestId,
                'donation_id' => $donationId,
                'donation_type' => $donation->type,
                'donation_quantity' => $donation->quantity,
                'request_type' => $request->type,
                'request_quantity' => $request->quantity,
            ]);

            // Use a database transaction to ensure atomicity
            DB::transaction(function () use ($request, $donation) {
                // Update the request status to 'fulfilled'
                $request->update(['status' => 'fulfilled']);

                // Update the donation status to 'assigned' and associate it with the request
                $donation->update(['status' => 'assigned', 'assigned_request_id' => $request->id]);

                // Log the successful assignment
                Log::info('Donation successfully assigned to request', [
                    'request_id' => $request->id,
                    'donation_id' => $donation->id,
                ]);
            });

            // // Optionally, send notifications to foodbank and donor
            // $request->foodbank->notify(new DonationAssigned($donation, $request));
            // $donation->donor->notify(new DonationAssignedToDonor($donation, $request));

            return response()->json(['message' => 'Donation successfully assigned to request'], 200);
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Failed to assign donation to request', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to assign donation', 'message' => $e->getMessage()], 500);
        }
    }


}