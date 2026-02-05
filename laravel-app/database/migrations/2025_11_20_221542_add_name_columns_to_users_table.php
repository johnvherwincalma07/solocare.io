<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Add new columns
        $table->string('first_name')->after('id');
        $table->string('middle_name')->nullable()->after('first_name');
        $table->string('last_name')->after('middle_name');

        // Remove old fullname column
        $table->dropColumn('fullname');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        // Rollback
        $table->string('fullname')->after('id');
        $table->dropColumn(['first_name', 'middle_name', 'last_name']);
    });
}

};
