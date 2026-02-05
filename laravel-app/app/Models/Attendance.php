<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
  // Table name
    protected $primaryKey = 'attendance_id'; // Custom primary key
    public $incrementing = true;           // Auto-increment
    protected $keyType = 'int';            // Primary key type

    protected $fillable = [
        'name',
        'type',
        'date',
        'time',
        'location',
        'max_participants',
        'participants',
        'status',
    ];

    protected $casts = [
        'participants' => 'array',         // Cast JSON to array
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];
}
