<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ready_to_process', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Ready', 'Approved', 'Rejected'])
                  ->default('Pending')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('ready_to_process', function (Blueprint $table) {
            $table->string('status')->default('Ready to Process')->change();
        });
    }
};
