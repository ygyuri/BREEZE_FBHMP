<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'foodbank_id', 'status', 'trial_ends_at', 'subscription_ends_at', 'monthly_fee'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'monthly_fee' => 'decimal:2',
    ];

    /**
     * Get the foodbank (User with the role 'foodbank') associated with the subscription.
     */
    public function foodbank()
    {
        return $this->belongsTo(User::class, 'foodbank_id')
                    ->where('role', 'foodbank');
    }

    /**
     * Get the donor (User with the role 'donor') associated with the subscription, if applicable.
     */
    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id')
                    ->where('role', 'donor');
    }

    /**
     * Scope a query to only include active subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to include expired subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope a query to include trial subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }
}