<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecoveryOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recovery_order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('recovery_order_id');
            $table->foreign('recovery_order_id')->references('id')->on('recovery_orders')->onDelete('cascade');
            $table->unsignedTinyInteger('stt')->nullable(); // STT
            $table->string('product_code')->nullable(); // Mã sản phẩm
            $table->string('product_name')->nullable(); // Tên sản phẩm
            $table->unsignedTinyInteger('quantity')->nullable(); // Số lượng (Lẻ)
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
        Schema::dropIfExists('recovery_order_details');
    }
}
