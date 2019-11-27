<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_request_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_request_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('total', 9, 2);
            $table->timestamps();

            $table->foreign('order_request_id')->references('id')->on('order_requests')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_request_details');
    }
}
