<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingRecoveryStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_recovery_staffs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('staff')->nullable();//nhân viên phụ trách 
            $table->string('recovery_code'); 
            $table->foreign('recovery_code')->references('recovery_code')->on('accounting_recoveries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_recovery_staffs');
    }
}
