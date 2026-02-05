<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $table = 'barangays';

    protected $fillable = [
        'name',
        'qr_code', // Optional: store QR code string or image URL
    ];

    // ------------------------
    // Relationships
    // ------------------------

    // Solo parent beneficiaries linked to this barangay
    public function beneficiaries()
    {
        return $this->hasMany(\App\Models\SoloParentBeneficiary::class, 'barangay_id', 'id');
    }

    // Payout schedules linked to this barangay
    public function schedules()
    {
        return $this->hasMany(PayoutSchedule::class, 'barangay_id', 'id');
    }
    
    // Optional: Payout schedules
    public function payoutSchedules()
    {
        return $this->hasMany(PayoutSchedule::class, 'barangay_id');
    }
}
