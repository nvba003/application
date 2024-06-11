<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemporaryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporary_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('temporary_id');
            $table->foreign('temporary_id')->references('id')->on('temporaries')->onDelete('cascade');
            $table->string('product_code')->nullable();
            $table->unsignedMediumInteger('sap_code')->nullable();
            $table->string('product_name')->nullable();
            $table->unsignedSmallInteger('quantity')->nullable(); // Số lượng (Lẻ)
            $table->unsignedTinyInteger('type')->comment('0: export, 1: import');
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
        Schema::dropIfExists('temporary_details');
    }
}
