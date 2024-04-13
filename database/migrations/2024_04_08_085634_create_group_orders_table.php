<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('group_id');//số nhóm
            $table->foreign('group_id')->references('id')->on('summary_orders')->onDelete('cascade');
            $table->unsignedInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('accounting_orders')->onDelete('set null');
            $table->unsignedInteger('recovery_id')->nullable();
            $table->foreign('recovery_id')->references('id')->on('recovery_orders')->onDelete('set null');
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
        Schema::dropIfExists('group_orders');
    }
}
