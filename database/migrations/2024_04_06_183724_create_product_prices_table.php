<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('product_code')->unique(); // Mã SP
            $table->unsignedMediumInteger('sap_code')->unique(); // Mã SAP
            $table->string('product_name'); // Tên sản phẩm
            $table->string('status'); // Trạng thái
            $table->string('packaging'); // Quy cách thùng
            $table->unsignedMediumInteger('price_sellin_per_pack'); // Giá Sellin thùng
            $table->unsignedMediumInteger('price_sellin_per_unit'); // Giá Sellin lẻ
            $table->unsignedMediumInteger('price_sellout_per_pack'); // Giá Sellout thùng
            $table->unsignedMediumInteger('price_sellout_per_unit'); // Giá Sellout lẻ
            $table->timestamps(); // Các trường created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices');
    }
}
