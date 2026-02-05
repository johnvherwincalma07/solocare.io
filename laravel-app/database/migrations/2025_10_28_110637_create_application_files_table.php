<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                  ->constrained('solo_parent_applications')
                  ->onDelete('cascade'); // delete files if application is deleted
            $table->string('path'); // file path
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_files');
    }
};
