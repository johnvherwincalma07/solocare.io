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
    Schema::table('solo_parent_applications', function (Blueprint $table) {
        $table->date('scheduled_date')->nullable()->after('status');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            //
        });
    }
};
