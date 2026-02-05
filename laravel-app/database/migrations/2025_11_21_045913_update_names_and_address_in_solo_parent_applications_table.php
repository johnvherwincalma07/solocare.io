<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn('full_name');
            $table->dropColumn('address');

            // Add new columns for names
            $table->string('last_name')->after('reference_no');
            $table->string('first_name')->after('last_name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('name_extension')->nullable()->after('middle_name');

            // Add new columns for address
            $table->string('street')->after('birth_date');
            $table->string('barangay')->after('street');
            $table->string('municipality')->after('barangay');
            $table->string('province')->after('municipality');
        });
    }

    public function down(): void
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['last_name', 'first_name', 'middle_name', 'name_extension', 'street', 'barangay', 'municipality', 'province']);

            // Restore old columns
            $table->string('full_name')->after('reference_no');
            $table->string('address')->after('birth_date');
        });
    }
};
