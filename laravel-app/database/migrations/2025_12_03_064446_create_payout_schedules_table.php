<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payout_schedules', function (Blueprint $table) {
            $table->id('schedule_payout_id');
            $table->string('barangay');
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->string('location');
            $table->enum('status', ['Pending', 'Scheduled', 'Completed'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_schedules');
    }
};
