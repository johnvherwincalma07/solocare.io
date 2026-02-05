<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            // Expand enum to include "Not Employed"
            $table->enum('employment_status', ['Employed', 'Unemployed', 'Self-Employed', 'Not Employed'])->change();
        });
    }

    public function down()
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            // Revert if needed
            $table->enum('employment_status', ['Employed', 'Unemployed', 'Self-Employed'])->change();
        });
    }
};
