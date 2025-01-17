<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * Display a listing of feedback with pagination and optional filtering.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info('Fetching feedback', ['request' => $request->all()]);

        $validated = $request->validate([
            'recipient_id' => 'nullable|integer|exists:users,id',
            'foodbank_id' => 'nullable|integer|exists:users,id',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        try {
            $feedbacks = Feedback::with(['recipient', 'foodbank'])
                ->when($validated['recipient_id'] ?? null, function ($query, $recipient_id) {
                    return $query->where('recipient_id', $recipient_id);
                })
                ->when($validated['foodbank_id'] ?? null, function ($query, $foodbank_id) {
                    return $query->where('foodbank_id', $foodbank_id);
                })
                ->when($validated['rating'] ?? null, function ($query, $rating) {
                    return $query->where('rating', $rating);
                })
                ->paginate(10);

            Log::info('Feedback fetched successfully', ['feedbacks' => $feedbacks]);

            return response()->json($feedbacks, 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch feedback', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch feedback', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created feedback in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('Creating feedback', ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|integer|exists:users,id',
            'foodbank_id' => 'required|integer|exists:users,id',
            'thank_you_note' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed while creating feedback', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $feedback = Feedback::create($validator->validated());

            Log::info('Feedback created successfully', ['feedback' => $feedback]);

            return response()->json(['message' => 'Feedback created successfully', 'data' => $feedback], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create feedback', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create feedback', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified feedback.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        Log::info("Fetching feedback with ID: $id");

        try {
            $feedback = Feedback::with(['recipient', 'foodbank'])->findOrFail($id);

            Log::info('Feedback fetched successfully', ['feedback' => $feedback]);

            return response()->json($feedback, 200);
        } catch (\Exception $e) {
            Log::error("Failed to fetch feedback with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Feedback not found', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Update the specified feedback in the database.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Log::info("Updating feedback with ID: $id", ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'thank_you_note' => 'sometimes|string|max:1000',
            'rating' => 'sometimes|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed while updating feedback with ID: $id", ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $feedback = Feedback::findOrFail($id);
            $feedback->update($validator->validated());

            Log::info('Feedback updated successfully', ['feedback' => $feedback]);

            return response()->json(['message' => 'Feedback updated successfully', 'data' => $feedback], 200);
        } catch (\Exception $e) {
            Log::error("Failed to update feedback with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update feedback', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified feedback from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Log::info("Deleting feedback with ID: $id");

        try {
            $feedback = Feedback::findOrFail($id);
            $feedback->delete();

            Log::info('Feedback deleted successfully', ['feedback' => $feedback]);

            return response()->json(['message' => 'Feedback deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Failed to delete feedback with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete feedback', 'message' => $e->getMessage()], 500);
        }
    }
}