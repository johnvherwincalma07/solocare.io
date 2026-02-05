<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadyToProcess extends Model
{
    use HasFactory;

    protected $table = 'ready_to_process';
    protected $primaryKey = 'ready_process_id';

    protected $fillable = [
        'application_id',
        'reference_no',
        'first_name',
        'last_name',
        'street',
        'barangay_id', // <- make sure this column exists
        'municipality',
        'category',
        'status',
    ];

    // ✅ Relationship to Barangay
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

    // Optional: relationship to the original application
    public function application()
    {
        return $this->belongsTo(SoloParentApplication::class, 'application_id');
    }
}
