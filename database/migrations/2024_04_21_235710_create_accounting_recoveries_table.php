<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingRecoveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_recoveries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('recovery_code')->unique();
            $table->string('staff')->nullable(); 
            $table->date('recovery_date')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('shop_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('type')->nullable();
            $table->text('recovery_reason')->nullable();
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
        Schema::dropIfExists('accounting_recoveries');
    }
}
