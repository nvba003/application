<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTemporariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_temporaries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('staff_id')->nullable();//nhân viên phụ trách 
            $table->foreign('staff_id')->references('id')->on('sale_staffs')->onDelete('set null');
            $table->unsignedMediumInteger('discount')->nullable();
            $table->unsignedMediumInteger('total_amount')->nullable();
            $table->date('report_date')->nullable();//ngày báo cáo
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
        Schema::dropIfExists('order_temporaries');
    }
}
