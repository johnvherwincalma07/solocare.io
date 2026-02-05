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
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('sender_id'); // user/admin who sent the message
        $table->unsignedBigInteger('receiver_id'); // who receives
        $table->text('message');
        $table->boolean('is_read')->default(false);
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
