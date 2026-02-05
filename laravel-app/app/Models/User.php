<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    // Role constants
public const ROLE_USER = 'user';
public const ROLE_ADMIN = 'admin';
public const ROLE_SUPER_ADMIN = 'super_admin';


    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'username',
        'email',
        'contact',
        'street',
        'barangay',
        'municipality_city',
        'province',
        'password',
        'role',
        'status',
        'otp',
        'otp_expires_at',
        'avatar', // filename only
    ];

    // Automatically hash passwords
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    // Virtual attributes
    protected $appends = ['full_address', 'avatar_url'];

    // Full address
    public function getFullAddressAttribute()
    {
        return trim("{$this->street}, {$this->barangay}, {$this->municipality_city}, {$this->province}");
    }

public function getAvatarUrlAttribute()
{
    if (!$this->avatar) return null;

    // Path in public_html/storage/avatars
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/storage/avatars/' . $this->avatar;

    if (!file_exists($filePath)) return null;

    // Return proper URL
    return asset('storage/avatars/' . $this->avatar) . '?v=' . filemtime($filePath);
}

}
