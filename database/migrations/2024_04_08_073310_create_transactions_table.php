<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('staff_id')->nullable();//nhân viên phụ trách 
            $table->foreign('staff_id')->references('id')->on('sale_staffs')->onDelete('set null');
            $table->unsignedMediumInteger('total_amount')->nullable();
            $table->unsignedSmallInteger('diff_amount')->nullable();//different amount
            $table->date('pay_date')->nullable();//ngày trả tiền = ngày báo cáo
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
