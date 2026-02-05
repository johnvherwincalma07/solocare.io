<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_id',       // updated to use barangay_id
        'scheduled_date',
        'scheduled_time',
        'location',
        'status',
    ];

    // ------------------------
    // Relationships
    // ------------------------

    // Barangay relationship
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

    // Beneficiaries scheduled for this payout
    public function beneficiaries()
    {
        return $this->hasMany(SoloParentBeneficiary::class, 'barangay_id', 'barangay_id');
    }
}
