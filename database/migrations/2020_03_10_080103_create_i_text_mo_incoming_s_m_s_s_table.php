<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateITextMoIncomingSMSSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_text_mo_incoming_s_m_s_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('originator');
            $table->string('gateway');
            $table->string('message');
            $table->string('timestamp');
            
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
        Schema::dropIfExists('i_text_mo_incoming_s_m_s_s');
    }
}
