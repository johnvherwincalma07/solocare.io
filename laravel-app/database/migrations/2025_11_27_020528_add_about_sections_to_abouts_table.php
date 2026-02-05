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
    Schema::table('abouts', function (Blueprint $table) {
        $table->longText('content_qualified')->nullable(); // CARD 2
        $table->longText('content_benefits')->nullable();  // CARD 3
    });
}

public function down()
{
    Schema::table('abouts', function (Blueprint $table) {
        $table->dropColumn(['content_qualified', 'content_benefits']);
    });
}

};
