<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('home_visits', function (Blueprint $table) {
            // Rename the primary key column without dropping it
            $table->renameColumn('id', 'visit_id');
        });
    }

    public function down()
    {
        Schema::table('home_visits', function (Blueprint $table) {
            $table->renameColumn('visit_id', 'id');
        });
    }
};
