<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            'type' => 'nullable|in:food,clothing,money',
            'donor_id' => 'nullable|exists:users,id',
            'foodbank_id' => 'nullable|exists:users,id',
            'recipient_id' => 'nullable|exists:users,id',
        ]);

        try {
            $donations = Donation::with(['donor', 'foodbank', 'recipient'])
                ->when($validated['type'] ?? null, fn($query, $type) => $query->where('type', $type))
                ->when($validated['donor_id'] ?? null, fn($query, $donorId) => $query->where('donor_id', $donorId))
                ->when($validated['foodbank_id'] ?? null, fn($query, $foodbankId) => $query->where('foodbank_id', $foodbankId))
                ->when($validated['recipient_id'] ?? null, fn($query, $recipientId) => $query->where('recipient_id', $recipientId))
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
            'type' => 'required|in:food,clothing,money',
            'quantity' => 'required|integer|min:1',
            'donor_id' => 'required|exists:users,id',
            'foodbank_id' => 'required|exists:users,id',
            'recipient_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed while creating donation', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Add 'status' to validated data and set it to 'pending' by default
            $validatedData = $validator->validated();
            $validatedData['status'] = $validatedData['recipient_id'] ? 'assigned' : 'pending';

            $donation = Donation::create($validatedData);

            Log::info('Donation created successfully', ['donation' => $donation]);

            return response()->json($donation, 201);
        } catch (\Exception $e) {
            Log::error('Failed to create donation', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create donation', 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified donation.
     *
     * @param Donation $donation
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Donation $donation)
    {
        try {
            return response()->json($donation->load(['donor', 'foodbank', 'recipient']), 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch donation', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch donation', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified donation in the database.
     *
     * @param Request $request
     * @param Donation $donation
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Donation $donation)
    {
        Log::info('Updating donation', ['request' => $request->all(), 'donation_id' => $donation->id]);

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:food,clothing,money',
            'quantity' => 'nullable|integer|min:1',
            'donor_id' => 'nullable|exists:users,id',
            'foodbank_id' => 'nullable|exists:users,id',
            'recipient_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,assigned,delivered',  // Ensure valid status is passed
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed while updating donation', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Update status if recipient is set, otherwise keep 'pending'
            $validatedData = $validator->validated();
            if (isset($validatedData['recipient_id'])) {
                $validatedData['status'] = 'assigned';
            }

            $donation->update($validatedData);

            Log::info('Donation updated successfully', ['donation' => $donation]);

            return response()->json($donation, 200);
        } catch (\Exception $e) {
            Log::error('Failed to update donation', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update donation', 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified donation from the database.
     *
     * @param Donation $donation
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Donation $donation)
    {
        try {
            $donation->delete();

            Log::info('Donation deleted successfully', ['donation_id' => $donation->id]);

            return response()->json(['message' => 'Donation deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete donation', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete donation', 'message' => $e->getMessage()], 500);
        }
    }


    // Mark a donation as completed
        public function assignRecipient(Request $request, Donation $donation, $recipientId)
    {
        // Check if the recipient exists
        $recipient = User::find($recipientId);
        if (!$recipient) {
            return response()->json(['error' => 'Recipient not found'], 404);
        }

        // Assign the recipient and change the donation status to 'assigned'
        try {
            $donation->recipient_id = $recipientId;
            $donation->status = 'assigned'; // Update status
            $donation->save();

            Log::info('Donation assigned to recipient', ['donation_id' => $donation->id, 'recipient_id' => $recipientId]);

            return response()->json(['message' => 'Donation assigned successfully', 'donation' => $donation], 200);
        } catch (\Exception $e) {
            Log::error('Failed to assign recipient to donation', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to assign recipient', 'message' => $e->getMessage()], 500);
        }
    }


    // Mark a donation as completed
        public function markAsCompleted(Donation $donation)
    {
        // Check if the donation is already completed
        if ($donation->status === 'completed') {
            return response()->json(['message' => 'Donation is already completed'], 400);
        }

        // Mark the donation as completed
        try {
            $donation->status = 'completed'; // Update status to 'completed'
            $donation->save();

            Log::info('Donation marked as completed', ['donation_id' => $donation->id]);

            return response()->json(['message' => 'Donation marked as completed', 'donation' => $donation], 200);
        } catch (\Exception $e) {
            Log::error('Failed to mark donation as completed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to mark donation as completed', 'message' => $e->getMessage()], 500);
        }
    }


}