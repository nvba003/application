<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemporariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporaries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('temporary_code')->nullable();
            $table->string('staff')->nullable();
            $table->dateTime('creation_date')->nullable();
            $table->dateTime('approval_date')->nullable();
            $table->tinyInteger('type')->comment('0: export, 1: import');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temporaries');
    }
}
