<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedSmallInteger('group_promotion_id');
            $table->unsignedSmallInteger('promotion_serial')->nullable();//số thứ tự promotion
            $table->text('description')->nullable();
            $table->enum('promotion_type', ['Chiết khấu', 'Sản phẩm'])->default('Chiết khấu');
            $table->unsignedSmallInteger('minimum_quantity')->nullable();//số lượng tối thiểu
            $table->unsignedMediumInteger('minimum_amount')->nullable();//số tiền tối thiểu
            $table->decimal('discount_percentage', 5, 2)->nullable();//% giảm giá
            $table->unsignedSmallInteger('bonus_product_id')->nullable();//sản phẩm tặng, liên kết sap_code
            $table->unsignedSmallInteger('bonus_quantity')->nullable();//số lượng tặng
            $table->decimal('bonus_ratio', 5, 2)->nullable();//tỉ lệ tặng
            $table->timestamps();

            $table->foreign('group_promotion_id')->references('id')->on('promotion_groups')->onDelete('cascade');
            $table->foreign('bonus_product_id')->references('sap_code')->on('product_prices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}
