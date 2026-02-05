<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_visits', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('home_visits', 'full_name')) {
                $table->dropColumn('full_name');
            }
            if (Schema::hasColumn('home_visits', 'address')) {
                $table->dropColumn('address');
            }

            // Add new columns
            $table->string('last_name')->after('reference_no');
            $table->string('first_name')->after('last_name');
            $table->string('barangay')->after('first_name');
        });
    }

    public function down(): void
    {
        Schema::table('home_visits', function (Blueprint $table) {
            // Remove the new columns
            $table->dropColumn(['last_name', 'first_name', 'barangay']);

            // Restore old columns
            $table->string('full_name')->after('reference_no');
            $table->string('address')->after('full_name');
        });
    }
};
