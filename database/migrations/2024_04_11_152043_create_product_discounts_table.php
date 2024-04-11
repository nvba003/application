<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedMediumInteger('sap_code'); // Mã SAP
            $table->foreign('sap_code')->references('sap_code')->on('product_prices');
            $table->string('product_name')->nullable(); // Tên sản phẩm
            $table->unsignedMediumInteger('price')->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable(); // Phần trăm chiết khấu
            $table->unsignedMediumInteger('discounted_price')->nullable(); // Giá sau chiết khấu cho mỗi đơn vị sản phẩm
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
        Schema::dropIfExists('product_discounts');
    }
}
