<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsBeneficiaryToSoloParentApplications extends Migration
{
    public function up()
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            $table->boolean('is_beneficiary')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('solo_parent_applications', function (Blueprint $table) {
            $table->dropColumn('is_beneficiary');
        });
    }
}
