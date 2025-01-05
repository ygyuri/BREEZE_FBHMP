<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes;

    protected $table = 'users';

    /**
     * Mass assignable attributes.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'location',
        'organization_name',
        'recipient_type',
        'donor_type',
        'phone',
        'address',
        'notes',
    ];

    /**
     * Attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier for JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return custom claims for JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Scope a query to only include users with a specific role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Sanitize user input before saving.
     *
     * @param array $data
     * @return array
     */
    public static function sanitizeInput(array $data)
    {
        return [
            'name' => e($data['name'] ?? null),
            'email' => $data['email'] ?? null,
            'phone' => isset($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null,
            'password' => isset($data['password']) ? bcrypt($data['password']) : null,
            'role' => $data['role'] ?? null,
            'location' => e($data['location'] ?? null),
            'address' => e($data['address'] ?? null),
            'organization_name' => e($data['organization_name'] ?? null),
            'recipient_type' => $data['recipient_type'] ?? null,
            'donor_type' => e($data['donor_type'] ?? null),
            'notes' => e($data['notes'] ?? null),
        ];
    }
}
