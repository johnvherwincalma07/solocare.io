<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;



    protected $primaryKey = 'event_id';

    protected $fillable = [
        'name',
        'type', // Seminar, Event, Meeting, Home Visit
        'date',
        'time',
        'location',
        'max_participants',
        'status', // Pending, Ongoing, Completed
    ];

    // Optional: Accessor for full date-time
    public function getDateTimeAttribute()
    {
        return $this->date . ' ' . $this->time;
    }
}
