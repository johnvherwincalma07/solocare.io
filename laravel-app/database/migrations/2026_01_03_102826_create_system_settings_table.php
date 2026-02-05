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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            // Branding
            $table->string('system_brand_name')->default('Solo Care');
            $table->string('system_full_name')->default('SOLO PARENT INFORMATION AND ASSISTANCE SYSTEM');

            // General info
            $table->text('system_description')->nullable();
            $table->string('admin_email')->nullable();

            // Footer
            $table->string('footer_text')->nullable();

            // Logo
            $table->string('system_logo')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
