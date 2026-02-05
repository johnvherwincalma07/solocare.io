<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoloParentBeneficiary extends Model
{
    protected $primaryKey = 'beneficiary_id'; // primary key

    // Mass assignable fields
    protected $fillable = [
        'application_id',
        'first_name',
        'last_name',
        'street',
        'barangay_id', // updated to use barangay_id
        'municipality',
        'date_added',
        'assistance_status',
        'category',
        'selected_benefits',
    ];

    public $timestamps = true;

    protected $casts = [
        'date_added' => 'datetime',
        'selected_benefits' => 'array',
    ];

    // ------------------------
    // Relationships
    // ------------------------

    // Solo parent application
    public function application()
    {
        return $this->belongsTo(SoloParentApplication::class, 'application_id', 'application_id');
    }

    // Solo parent benefits (one-to-many)
    public function benefits()
    {
        return $this->hasMany(BeneficiaryBenefit::class, 'beneficiary_id', 'beneficiary_id');
    }

    // Barangay relationship
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

}
