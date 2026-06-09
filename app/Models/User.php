<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'password',
        'role',
        'signature_image',
        // --- NEW FIELDS ADDED HERE ---
        'is_qualified',
        'is_verified',
        'assigned_hall',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // --- ADD CASTS FOR BOOTLEANS ---
            'is_qualified' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Connects a user record directly to their student profile metadata table.
     */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }
}