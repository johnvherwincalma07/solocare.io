<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationFile extends Model
{
    use HasFactory;

    protected $primaryKey = 'documents_id'; // your primary key
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'application_id',
        'path',
    ];

    // Relationship to SoloParentApplication
    public function application()
    {
        return $this->belongsTo(SoloParentApplication::class, 'application_id', 'application_id');
    }

    // Helper to get full storage URL
    public function getUrlAttribute()
    {
        return $this->path ? asset('storage/' . $this->path) : null;
    }
}
