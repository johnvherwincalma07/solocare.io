<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id('activity_id'); // primary key as activity_id
            $table->unsignedBigInteger('user_id')->nullable(); // user performing the activity
            $table->string('activity'); // description of the activity
            $table->string('status')->default('Completed'); // e.g., Completed, Failed, Pending
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
