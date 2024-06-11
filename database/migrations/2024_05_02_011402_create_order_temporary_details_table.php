<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTemporaryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_temporary_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_temporary_id');
            $table->foreign('order_temporary_id')->references('id')->on('order_temporaries')->onDelete('cascade');
            $table->string('product_code')->nullable(); // Mã SP
            $table->unsignedMediumInteger('sap_code')->nullable(); // Mã SAP
            $table->string('product_name')->nullable(); // Tên sản phẩm
            $table->unsignedSmallInteger('packing')->nullable();//quy cách đóng gói
            $table->unsignedMediumInteger('price')->nullable();
            $table->unsignedSmallInteger('thung')->nullable();
            $table->unsignedSmallInteger('le')->nullable();
            $table->unsignedSmallInteger('quantity')->nullable();//packing * thung + le
            $table->unsignedMediumInteger('subtotal')->nullable();//thành tiền = price * quantity
            $table->decimal('discount_percentage', 5, 2)->nullable(); // Phần trăm chiết khấu
            $table->unsignedMediumInteger('discounted_price')->nullable(); // Giá sau chiết khấu = price * discount_percentage/100
            $table->unsignedMediumInteger('discount')->nullable();//giảm tiền = subtotal - payable
            $table->unsignedMediumInteger('payable')->nullable();//thanh toán = discounted_price * quantity
            $table->boolean('is_gift')->default(false);
            $table->unsignedMediumInteger('gift_sap_code')->nullable();
            $table->unsignedSmallInteger('promotion_id')->nullable();
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('set null');
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
        Schema::dropIfExists('order_temporary_details');
    }
}
