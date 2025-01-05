<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Donation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'donor_id',
        'foodbank_id',
        'recipient_id',
        'type',
        'quantity',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'type' => 'string',
    ];

    /**
     * Boot method for the model to log events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($donation) {
            Log::info('Creating Donation', ['donation' => $donation]);
        });

        static::created(function ($donation) {
            Log::info('Donation Created Successfully', ['donation' => $donation]);
        });

        static::updating(function ($donation) {
            Log::info('Updating Donation', ['donation' => $donation]);
        });

        static::updated(function ($donation) {
            Log::info('Donation Updated Successfully', ['donation' => $donation]);
        });

        static::deleting(function ($donation) {
            Log::info('Deleting Donation', ['donation' => $donation]);
        });

        static::deleted(function ($donation) {
            Log::info('Donation Deleted Successfully', ['donation' => $donation]);
        });
    }

    /**
     * Get the donor associated with the donation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id')->withTrashed();
    }

    /**
     * Get the foodbank associated with the donation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function foodbank()
    {
        return $this->belongsTo(User::class, 'foodbank_id')->withTrashed();
    }

    /**
     * Get the recipient associated with the donation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id')->withTrashed();
    }

    /**
     * Scope a query to only include donations of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter donations by donor.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $donorId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDonor($query, int $donorId)
    {
        return $query->where('donor_id', $donorId);
    }

    /**
     * Scope a query to filter donations by recipient.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $recipientId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByRecipient($query, int $recipientId)
    {
        return $query->where('recipient_id', $recipientId);
    }

    /**
     * Scope a query to filter donations by foodbank.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $foodbankId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFoodbank($query, int $foodbankId)
    {
        return $query->where('foodbank_id', $foodbankId);
    }
}
