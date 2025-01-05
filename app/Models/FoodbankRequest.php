<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodbankRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'food_type',
        'quantity',
        'notes',
        'status', // pending, approved, rejected
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
