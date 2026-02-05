<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'system_brand_name',
        'system_full_name',
        'system_description',
        'admin_email',
        'footer_text',
        'system_logo',
    ];
}
