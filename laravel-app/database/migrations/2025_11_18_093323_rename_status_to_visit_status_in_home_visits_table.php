<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_visits', function (Blueprint $table) {
            $table->renameColumn('status', 'visit_status');
        });
    }

    public function down(): void
    {
        Schema::table('home_visits', function (Blueprint $table) {
            $table->renameColumn('visit_status', 'status');
        });
    }
};
