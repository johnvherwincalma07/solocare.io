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
    Schema::create('solo_parent_beneficiaries', function (Blueprint $table) {
        $table->id();
        $table->string('resident_name');
        $table->string('address');
        $table->string('barangay');
        $table->date('date_added')->nullable();
        $table->string('assistance_status')->default('Pending');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solo_parent_beneficiaries');
    }
};
