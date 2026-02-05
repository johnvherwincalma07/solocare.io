<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            // Rename the primary key column
            $table->renameColumn('id', 'application_id');
        });
    }

    public function down(): void
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            // Revert the column name
            $table->renameColumn('application_id', 'id');
        });
    }
};
