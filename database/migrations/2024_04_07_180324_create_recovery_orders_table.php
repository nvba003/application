<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecoveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recovery_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('recovery_code')->unique(); // Mã phiếu
            $table->string('staff')->nullable(); // NVBH - Nhân viên bán hàng
            $table->timestamp('approval_date')->nullable(); // Ngày Duyệt
            $table->date('recovery_date')->nullable(); // Ngày thu hồi
            $table->date('recovery_creation_date')->nullable(); // Ngày tạo phiếu
            $table->string('status')->nullable(); // Trạng thái
            $table->unsignedMediumInteger('discount')->nullable();
            $table->unsignedMediumInteger('total_amount')->nullable();
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
        Schema::dropIfExists('recovery_orders');
    }
}
