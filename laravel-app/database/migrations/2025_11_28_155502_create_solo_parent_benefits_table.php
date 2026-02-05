<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoloParentBenefitsTable extends Migration
{
    public function up()
    {
        Schema::create('benefits', function (Blueprint $table) {
            $table->bigIncrements('benefit_id'); // primary key
            $table->string('title');
            $table->text('description');
            $table->integer('category_id')->nullable();
            $table->string('barangay')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('benefits');
    }
}
