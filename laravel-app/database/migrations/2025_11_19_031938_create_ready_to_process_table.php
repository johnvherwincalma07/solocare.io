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
        Schema::create('ready_to_process', function (Blueprint $table) {
            $table->bigIncrements('ready_process_id'); // Primary key
            $table->unsignedBigInteger('visit_id'); // FK to HomeVisit
            $table->unsignedBigInteger('application_id')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('full_name');
            $table->string('address')->nullable();
            $table->date('visit_date')->nullable();
            $table->time('visit_time')->nullable();
            $table->string('status')->default('Ready to Process');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('visit_id')->references('visit_id')->on('home_visits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ready_to_process');
    }
};
