<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 👈 1. Import Sanctum tokens trait

class User extends Authenticatable
{
    // 👈 2. Use HasApiTokens trait here so authentication functions work
    use HasApiTokens, HasFactory, Notifiable; 

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',     // Matches your form input
        'email',
        'phone_number',  // Matches your form input
        'password',
        'role',          // Matches your student assignment logic
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
        ];
    }

    // Connects a user record directly to their student profile metadata table
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }
}