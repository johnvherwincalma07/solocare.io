<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            // 🔹 ADD NEW ADDRESS FIELDS
            $table->string('street')->after('contact');
            $table->string('barangay')->after('street');
            $table->string('municipality_city')->after('barangay');
            $table->string('province')->after('municipality_city');

            // 🔹 REMOVE OLD SINGLE ADDRESS COLUMN
            $table->dropColumn('address');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            // 🔹 RESTORE SINGLE ADDRESS COLUMN
            $table->string('address')->after('contact');

            // 🔹 REMOVE NEW SPLIT FIELDS
            $table->dropColumn([
                'street',
                'barangay',
                'municipality_city',
                'province'
            ]);
        });
    }
};
