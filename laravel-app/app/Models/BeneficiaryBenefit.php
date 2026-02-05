<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryBenefit extends Model
{
    protected $primaryKey = 'beneficiary_benefit_id';


    protected $fillable = [
        'beneficiary_id',
        'benefit_name',
        'status',       // Add this
        'date_given',
        'remarks',
    ];


    public $timestamps = true;

    public function beneficiary()
    {
        return $this->belongsTo(SoloParentBeneficiary::class, 'beneficiary_id', 'beneficiary_id');
    }
}
