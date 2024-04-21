<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingRecoveryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_recovery_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('recovery_order_id');
            $table->foreign('recovery_order_id')->references('id')->on('accounting_recoveries')->onDelete('cascade');
            $table->unsignedTinyInteger('stt')->nullable(); // STT
            $table->string('product_code')->nullable(); // Mã sản phẩm
            $table->string('product_name')->nullable(); // Tên sản phẩm
            $table->unsignedTinyInteger('packing')->nullable();//quy cách đóng gói
            $table->unsignedTinyInteger('thung')->nullable();
            $table->unsignedTinyInteger('le')->nullable();
            $table->unsignedMediumInteger('price')->nullable();
            $table->unsignedMediumInteger('subtotal')->nullable();//thành tiền
            $table->unsignedMediumInteger('discount')->nullable();//giảm tiền
            $table->unsignedMediumInteger('payable')->nullable();//thanh toán
            $table->string('recovery_reason')->nullable(); // Lý do thu hồi
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
        Schema::dropIfExists('accounting_recovery_details');
    }
}
