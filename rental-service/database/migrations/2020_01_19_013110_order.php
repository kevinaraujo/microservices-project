<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Order extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->string('status');
            $table->float('discount');
            $table->float('downpayment');
            $table->float('delivery_fee');
            $table->float('late_fee');
            $table->float('total')->default(0);
            $table->float('balance')->default(0);
            $table->date('order_date');
            $table->date('return_date');
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
        Schema::dropIfExists('orders');
    }
}
