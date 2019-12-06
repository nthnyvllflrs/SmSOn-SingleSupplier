<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifest_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('manifest_id');
            $table->unsignedBigInteger('order_request_id');
            $table->timestamps();

            $table->foreign('manifest_id')->references('id')->on('manifests')->onDelete('cascade');
            $table->foreign('order_request_id')->references('id')->on('order_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manifest_details');
    }
}
