<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DonationController extends Controller
{
    /**
     * Display a listing of donations with pagination and optional filtering.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info('Fetching donations', ['request' => $request->all()]);

        $validated = $request->validate([
            'type' => 'nullable|string',
            'donor_id' => 'nullable|integer|exists:users,id',
            'foodbank_id' => 'nullable|integer|exists:users,id',
            'recipient_id' => 'nullable|integer|exists:users,id',
        ]);

        try {
            $donations = Donation::with(['donor', 'foodbank', 'recipient'])
                ->when($validated['type'] ?? null, fn($query, $type) => $query->ofType($type))
                ->when($validated['donor_id'] ?? null, fn($query, $donorId) => $query->byDonor($donorId))
                ->when($validated['foodbank_id'] ?? null, fn($query, $foodbankId) => $query->byFoodbank($foodbankId))
                ->when($validated['recipient_id'] ?? null, fn($query, $recipientId) => $query->byRecipient($recipientId))
                ->paginate(10);

            Log::info('Donations fetched successfully', ['donations' => $donations]);

            return response()->json($donations, 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch donations', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch donations', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created donation in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('Creating donation', ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'donor_id' => 'required|integer|exists:users,id',
            'foodbank_id' => 'required|integer|exists:users,id',
            'recipient_id' => 'nullable|integer|exists:users,id',
            'type' => 'required|string|max:50',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed while creating donation', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $donation = Donation::create($validator->validated());

            Log::info('Donation created successfully', ['donation' => $donation]);

            return response()->json(['message' => 'Donation created successfully', 'data' => $donation], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create donation', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create donation', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified donation.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        Log::info("Fetching donation with ID: $id");

        try {
            $donation = Donation::with(['donor', 'foodbank', 'recipient'])->findOrFail($id);

            Log::info('Donation fetched successfully', ['donation' => $donation]);

            return response()->json($donation, 200);
        } catch (\Exception $e) {
            Log::error("Failed to fetch donation with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Donation not found', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Update the specified donation in the database.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Log::info("Updating donation with ID: $id", ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|string|max:50',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed while updating donation with ID: $id", ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $donation = Donation::findOrFail($id);
            $donation->update($validator->validated());

            Log::info('Donation updated successfully', ['donation' => $donation]);

            return response()->json(['message' => 'Donation updated successfully', 'data' => $donation], 200);
        } catch (\Exception $e) {
            Log::error("Failed to update donation with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update donation', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified donation from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Log::info("Deleting donation with ID: $id");

        try {
            $donation = Donation::findOrFail($id);
            $donation->delete();

            Log::info('Donation deleted successfully', ['donation_id' => $id]);

            return response()->json(['message' => 'Donation deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Failed to delete donation with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete donation', 'message' => $e->getMessage()], 500);
        }
    }
}
