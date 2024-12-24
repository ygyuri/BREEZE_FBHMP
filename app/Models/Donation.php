<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Import SoftDeletes trait

class Donation extends Model
{
    use HasFactory, SoftDeletes; // Add SoftDeletes to enable soft deletion

    protected $fillable = [
        'donor_id', 'foodbank_id', 'recipient_id', 'type', 'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer', // Ensure quantity is always treated as an integer
        'type' => 'string', // Ensure 'type' is always treated as a string
    ];

    // Relationships
    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function foodbank()
    {
        return $this->belongsTo(User::class, 'foodbank_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}