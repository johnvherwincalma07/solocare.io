<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledSubmission extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_req_id';
    public $timestamps = true;

    protected $fillable = [
        'application_id',
        'reference_no',
        'first_name',
        'last_name',
        'street',
        'barangay',
        'municipality',
        'scheduled_date',
        'scheduled_time',
        'status',
        'category',
    ];
}
