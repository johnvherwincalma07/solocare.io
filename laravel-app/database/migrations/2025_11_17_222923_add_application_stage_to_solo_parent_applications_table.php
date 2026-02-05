<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            $table->string('application_stage')->default('Review Application')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            $table->dropColumn('application_stage');
        });
    }
};
