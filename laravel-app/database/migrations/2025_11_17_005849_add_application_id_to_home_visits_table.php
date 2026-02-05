<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('home_visits', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->nullable()->after('id');
            $table->foreign('application_id')
                  ->references('id')
                  ->on('solo_parent_applications')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('home_visits', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropColumn('application_id');
        });
    }
};
