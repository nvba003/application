<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummaryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_code')->unique()->nullable();
            $table->boolean('is_group')->default(false);//là nhóm thật
            $table->unsignedBigInteger('transaction_id')->nullable();//mã giao dịch
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
            $table->boolean('is_entered')->default(false);
            $table->date('report_date')->nullable();//ngày báo cáo
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
        Schema::dropIfExists('summary_orders');
    }
}
