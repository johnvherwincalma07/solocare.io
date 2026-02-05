<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    // Primary key (if not 'id')
    protected $primaryKey = 'beneficiary_id';

    // Fillable columns
    protected $fillable = [
        'application_id',
        'first_name',
        'last_name',
        'barangay',
        'date_added',
        'assistance_status',
    ];

    // Optional: If your primary key is not auto-incrementing integer
    // public $incrementing = false;

    // Optional: If you want to manage timestamps manually
    public $timestamps = true; // keeps created_at & updated_at
}
