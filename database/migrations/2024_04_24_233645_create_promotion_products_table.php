<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_products', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedSmallInteger('group_promotion_id')->nullable();
            $table->unsignedMediumInteger('sap_code')->nullable(); // Mã SAP
            $table->foreign('sap_code')->references('sap_code')->on('product_prices')->onDelete('set null');
            $table->string('product_name')->nullable(); // Tên sản phẩm
            $table->unsignedSmallInteger('parent_id')->nullable();
            $table->timestamps();

            $table->foreign('group_promotion_id')->references('id')->on('promotion_groups')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('promotion_products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_products');
    }
}
