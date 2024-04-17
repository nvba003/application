<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();//transaction_code
            $table->unsignedInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->unsignedTinyInteger('staff_id')->nullable();//nhân viên phụ trách 
            $table->foreign('staff_id')->references('id')->on('sale_staffs')->onDelete('set null');
            $table->unsignedMediumInteger('transfer_amount')->nullable();//chuyển khoản
            $table->unsignedSmallInteger('note_500')->nullable();
            $table->unsignedSmallInteger('note_200')->nullable();
            $table->unsignedSmallInteger('note_100')->nullable();
            $table->unsignedSmallInteger('note_50')->nullable();
            $table->unsignedSmallInteger('note_20')->nullable();
            $table->unsignedSmallInteger('note_10')->nullable();
            $table->unsignedSmallInteger('note_5')->nullable();
            $table->unsignedSmallInteger('note_2')->nullable();
            $table->unsignedSmallInteger('note_1')->nullable();
            $table->unsignedMediumInteger('cash')->nullable();//tiền mặt
            $table->unsignedMediumInteger('total_amount')->nullable();
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
        Schema::dropIfExists('transaction_details');
    }
}
