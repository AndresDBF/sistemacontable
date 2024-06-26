<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetencionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retencions', function (Blueprint $table) {
            $table->id('idret');
            $table->unsignedBigInteger('idpag');
            $table->foreign('idpag')
                  ->references('idpag')
                  ->on('comprobante_pagos');
            $table->unsignedBigInteger('idasi');
            $table->foreign('idasi')
                  ->references('idasi')
                  ->on('asientos');
            $table->unsignedBigInteger('idprov');
            $table->foreign('idprov')
                  ->references('idprov')
                  ->on('proveedors');
            $table->unsignedBigInteger('idorpa');
            $table->foreign('idorpa')
                  ->references('idorpa')
                  ->on('orden_pagos');
            $table->string('ncomprobante');
            $table->date('fecemi')->format('Y-m-d');
            $table->date('fecrecep')->format('Y-m-d');
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
        Schema::dropIfExists('retencions');
    }
}
