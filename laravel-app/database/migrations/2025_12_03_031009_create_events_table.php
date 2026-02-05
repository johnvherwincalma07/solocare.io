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
        Schema::create('events', function (Blueprint $table) {
    $table->id('event_id');
    $table->string('name');
    $table->enum('type', ['Seminar','Event','Meeting','Home Visit']);
    $table->date('date');
    $table->time('time');
    $table->string('location');
    $table->integer('max_participants')->default(0);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
