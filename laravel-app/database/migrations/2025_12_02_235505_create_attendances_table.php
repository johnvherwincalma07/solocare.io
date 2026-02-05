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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id('attendance_id');            // Primary key
            $table->string('name');                 // Activity name
            $table->string('type');                 // Seminar, Event, Meeting, Home Visit
            $table->date('date');                   // Activity date
            $table->time('time');                   // Activity time
            $table->string('location');             // Venue
            $table->integer('max_participants')->nullable(); // Maximum attendees
            $table->json('participants')->nullable();        // Store registered users (optional)
            $table->string('status')->default('Pending');   // Pending / Completed
            $table->timestamps();                  // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
