<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $application_id
 * @property int $user_id
 * @property string $reference_no
 * @property string $status
 * @property string $application_stage
 * @property string $category
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $name_extension
 * @property string $full_name
 * @property string $sex
 * @property int $age
 * @property string $birth_date
 * @property string $place_of_birth
 * @property string $street
 * @property string $barangay
 * @property string $municipality
 * @property string $province
 * @property string $employment_status
 * @property string $pantawid
 * @property string $indigenous_person
 * @property string $lgbtq
 * @property string $pwd
 * @property string $solo_parent_reason
 * @property string $solo_parent_needs
 * @property string $emergency_name
 * @property string $emergency_relationship
 * @property string $emergency_address
 * @property string $emergency_contact
 */
class SoloParentApplication extends Model
{
    use HasFactory;

    // Specify the table name if not standard
    protected $table = 'solo_parent_applications';

    // Set primary key to application_id
    protected $primaryKey = 'application_id';

    // Indicate if the PK is auto-incrementing
    public $incrementing = true;

    // Specify the key type
    protected $keyType = 'int';

    // Mass assignable fields
    protected $fillable = [
        'user_id',
        'reference_no',
        'last_name',
        'first_name',
        'middle_name',
        'name_extension',
        'full_name',
        'sex',
        'age',
        'place_of_birth',
        'birth_date',
        'street',
        'barangay',
        'municipality',
        'province',
        'educational_attainment',
        'civil_status',
        'occupation',
        'religion',
        'company_agency',
        'monthly_income',
        'employment_status',
        'contact_number',
        'email',
        'pantawid',
        'indigenous_person',
        'lgbtq',
        'pwd',
        'family',
        'solo_parent_reason',
        'solo_parent_needs',
        'emergency_name',
        'emergency_relationship',
        'emergency_address',
        'emergency_contact',
        'category',
        'status',
        'rejection_reason',
        'application_stage',
        'is_submitted',
        'declaration',
    ];

    // Cast JSON fields
    protected $casts = [
        'family' => 'array',
        'declaration' => 'boolean',
        'is_submitted' => 'boolean',
    ];

    public function getFullNameAttribute()
    {
        return strtoupper(trim(
            $this->last_name . ', ' .
            $this->first_name . ' ' .
            ($this->middle_name ?? '') . ' ' .
            ($this->name_extension ?? '')
        ));
    }

    protected $appends = ['address']; // <-- Add this

    public function getAddressAttribute()
    {
        return "{$this->street}, {$this->barangay}, {$this->municipality}, {$this->province}";
    }

    public function files()
    {
        return $this->hasMany(ApplicationFile::class, 'application_id', 'application_id');
    }

    // Optional: relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
