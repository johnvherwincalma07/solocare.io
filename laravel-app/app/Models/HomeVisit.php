<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeVisit extends Model
{
    use HasFactory;

    protected $primaryKey = 'visit_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'application_id',
        'reference_no',
        'first_name',
        'last_name',
        'street',
        'barangay',
        'municipality',
        'category',
        'visit_status',
        'visit_date',
        'visit_time',
        'sms_sent',
        'scheduled_at',
    ];

    public function application()
    {
        return $this->belongsTo(SoloParentApplication::class, 'application_id');
    }


}
