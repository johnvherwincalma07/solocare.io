<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'link',
        'type',
        'event_id',
        'status' // optional, e.g., "Active" or "Inactive"
    ];

    // If you want to relate it to the Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
