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
Schema::create('beneficiaries', function (Blueprint $table) {
    $table->id();
    $table->string('full_name');
    $table->string('address');
    $table->string('barangay');
    $table->date('date_approved')->nullable();
    $table->time('time_approved')->nullable();
    $table->string('status')->default('Approved');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
