<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('accounting_orders')->onDelete('cascade');
            $table->unsignedTinyInteger('stt')->nullable();
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->unsignedTinyInteger('packing')->nullable();//quy cách đóng gói
            $table->unsignedMediumInteger('price')->nullable();
            $table->unsignedTinyInteger('thung')->nullable();
            $table->unsignedTinyInteger('le')->nullable();
            $table->unsignedMediumInteger('subtotal')->nullable();//thành tiền
            $table->unsignedMediumInteger('discount')->nullable();//giảm tiền
            $table->unsignedMediumInteger('payable')->nullable();//thanh toán
            $table->boolean('is_special')->default(false); // Thêm cột is_special với kiểu boolean
            $table->text('notes')->nullable(); // Thêm cột notes với kiểu text và cho phép giá trị NULL
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
        Schema::dropIfExists('accounting_order_details');
    }
}
