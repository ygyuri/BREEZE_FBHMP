<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory, SoftDeletes; // Enable soft deletes


    protected $table = 'feedbacks'; // Explicitly define the table name
    protected $fillable = [
        'recipient_id', 'foodbank_id', 'thank_you_note', 'rating',
    ];

    protected $casts = [
        'rating' => 'integer', // Ensure the rating is always cast to an integer
    ];

    /**
     * Get the recipient that owns the feedback.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the foodbank that owns the feedback.
     */
    public function foodbank()
    {
        return $this->belongsTo(User::class, 'foodbank_id');
    }
}