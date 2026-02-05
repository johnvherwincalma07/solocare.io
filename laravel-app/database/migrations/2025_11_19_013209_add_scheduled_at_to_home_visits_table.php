<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('home_visits', function (Blueprint $table) {
            // Records WHEN a visit was scheduled
            $table->dateTime('scheduled_at')->nullable()->after('visit_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_visits', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
        });
    }
};
