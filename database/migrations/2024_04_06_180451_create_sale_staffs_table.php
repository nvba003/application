<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_staffs', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone_number')->nullable();
            $table->string('position')->nullable();
            $table->string('final_char')->nullable(); // Ký tự cuối
            $table->string('customer_code')->unique()->nullable(); // Mã KH
            $table->string('parameter')->unique()->nullable(); // Tham số
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
        Schema::dropIfExists('sale_staffs');
    }
}
