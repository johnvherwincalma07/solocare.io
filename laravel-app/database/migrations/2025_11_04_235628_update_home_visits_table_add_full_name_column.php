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
        // Remove first and last name if they exist
        if (Schema::hasColumn('home_visits', 'first_name')) {
            $table->dropColumn('first_name');
        }
        if (Schema::hasColumn('home_visits', 'last_name')) {
            $table->dropColumn('last_name');
        }

        // Add full_name column
        $table->string('full_name')->after('reference_no');
    });
}

public function down(): void
{
    Schema::table('home_visits', function (Blueprint $table) {
        $table->dropColumn('full_name');
        $table->string('first_name');
        $table->string('last_name');
    });
}


};
