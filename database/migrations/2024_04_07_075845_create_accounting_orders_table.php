<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code')->nullable();//mã đơn hàng
            $table->string('staff')->nullable();//NVBH
            $table->string('source')->nullable();//Nguồn đặt
            $table->string('status')->nullable();//trạng thái
            $table->string('type')->nullable();//loại đơn
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->unsignedMediumInteger('discount')->nullable();
            $table->unsignedMediumInteger('total_amount')->nullable();
            $table->timestamp('order_date')->nullable();//ngày đặt
            $table->timestamp('delivery_date')->nullable();//ngày giao thực tế
            $table->timestamps(); // Tạo cột created_at và updated_at tự động
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_orders');
    }
}
