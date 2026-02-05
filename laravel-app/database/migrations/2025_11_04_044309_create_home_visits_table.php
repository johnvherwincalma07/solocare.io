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
    Schema::create('home_visits', function (Blueprint $table) {
        $table->id();
        $table->string('reference_no')->nullable();
        $table->string('first_name')->nullable();
        $table->string('last_name')->nullable();
        $table->string('address')->nullable();
        $table->string('status')->default('Pending');
        $table->date('visit_date')->nullable();
        $table->time('visit_time')->nullable();
        $table->timestamps();
    });

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_visits');
    }
};
