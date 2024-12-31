<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions with optional filtering and pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info('Fetching subscriptions', ['request' => $request->all()]);

        $validated = $request->validate([
            'foodbank_id' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|string|in:active,expired,trial',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $subscriptions = Subscription::with(['foodbank', 'donor'])
                ->when($validated['foodbank_id'] ?? null, function ($query, $foodbank_id) {
                    return $query->where('foodbank_id', $foodbank_id);
                })
                ->when($validated['status'] ?? null, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($validated['start_date'] ?? null, function ($query, $start_date) {
                    return $query->whereDate('trial_ends_at', '>=', $start_date);
                })
                ->when($validated['end_date'] ?? null, function ($query, $end_date) {
                    return $query->whereDate('subscription_ends_at', '<=', $end_date);
                })
                ->paginate(10);

            Log::info('Subscriptions fetched successfully', ['subscriptions' => $subscriptions]);

            return response()->json($subscriptions, 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch subscriptions', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch subscriptions', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created subscription in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('Creating subscription', ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'foodbank_id' => 'required|integer|exists:users,id',
            'status' => 'required|string|in:active,expired,trial',
            'trial_ends_at' => 'nullable|date',
            'subscription_ends_at' => 'nullable|date|after_or_equal:trial_ends_at',
            'monthly_fee' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed while creating subscription', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $subscription = Subscription::create($validator->validated());
            Log::info('Subscription created successfully', ['subscription' => $subscription]);

            return response()->json(['message' => 'Subscription created successfully', 'data' => $subscription], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create subscription', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified subscription.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        Log::info("Fetching subscription with ID: $id");

        try {
            $subscription = Subscription::with(['foodbank', 'donor'])->findOrFail($id);
            Log::info('Subscription fetched successfully', ['subscription' => $subscription]);

            return response()->json($subscription, 200);
        } catch (\Exception $e) {
            Log::error("Failed to fetch subscription with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Subscription not found', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Update the specified subscription in the database.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Log::info("Updating subscription with ID: $id", ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'foodbank_id' => 'sometimes|integer|exists:users,id',
            'status' => 'sometimes|string|in:active,expired,trial',
            'trial_ends_at' => 'sometimes|date',
            'subscription_ends_at' => 'sometimes|date|after_or_equal:trial_ends_at',
            'monthly_fee' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed while updating subscription with ID: $id", ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->update($validator->validated());
            Log::info('Subscription updated successfully', ['subscription' => $subscription]);

            return response()->json(['message' => 'Subscription updated successfully', 'data' => $subscription], 200);
        } catch (\Exception $e) {
            Log::error("Failed to update subscription with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update subscription', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified subscription from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Log::info("Deleting subscription with ID: $id");

        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->delete();
            Log::info('Subscription deleted successfully', ['subscription_id' => $id]);

            return response()->json(['message' => 'Subscription deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Failed to delete subscription with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete subscription', 'message' => $e->getMessage()], 500);
        }
    }
}