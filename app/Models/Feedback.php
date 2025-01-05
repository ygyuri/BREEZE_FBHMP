<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    // Explicit table name definition
    protected $table = 'feedbacks';

    // Mass assignable attributes
    protected $fillable = [
        'recipient_id',
        'foodbank_id',
        'thank_you_note',
        'rating',
    ];

    // Attribute casting
    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Relationship: Feedback belongs to a recipient (User)
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Relationship: Feedback belongs to a foodbank (User)
     */
    public function foodbank()
    {
        return $this->belongsTo(User::class, 'foodbank_id');
    }

    /**
     * Create a new feedback entry with logging.
     *
     * @param array $data
     * @return Feedback|null
     */
    public static function createFeedback(array $data)
    {
        try {
            $feedback = self::create($data);
            Log::info('Feedback created successfully', ['feedback' => $feedback]);
            return $feedback;
        } catch (\Exception $e) {
            Log::error('Failed to create feedback', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Update feedback with logging.
     *
     * @param array $data
     * @return bool
     */
    public function updateFeedback(array $data)
    {
        try {
            $this->update($data);
            Log::info('Feedback updated successfully', ['feedback' => $this]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update feedback', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Soft delete feedback with logging.
     *
     * @return bool|null
     */
    public function deleteFeedback()
    {
        try {
            $this->delete();
            Log::info('Feedback deleted successfully', ['feedback_id' => $this->id]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete feedback', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
