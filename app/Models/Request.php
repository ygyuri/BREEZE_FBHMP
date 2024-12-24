<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use HasFactory, SoftDeletes; // Enable soft deletes

    protected $fillable = [
        'foodbank_id', 'type', 'quantity'
    ];

    protected $casts = [
        'quantity' => 'integer', // Ensure quantity is always cast to an integer
    ];

    /**
     * Get the foodbank that owns the request.
     */
    public function foodbank()
    {
        return $this->belongsTo(User::class, 'foodbank_id');
    }
}