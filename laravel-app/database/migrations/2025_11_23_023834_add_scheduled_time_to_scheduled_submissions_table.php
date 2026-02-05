<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('scheduled_submissions', function (Blueprint $table) {
            $table->time('scheduled_time')->nullable()->after('scheduled_date');
        });
    }

    public function down()
    {
        Schema::table('scheduled_submissions', function (Blueprint $table) {
            $table->dropColumn('scheduled_time');
        });
    }
};
