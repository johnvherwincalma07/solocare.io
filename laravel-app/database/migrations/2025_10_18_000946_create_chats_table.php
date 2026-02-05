<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->text('message');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
