<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagoNominasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago_nominas', function (Blueprint $table) {
            $table->id('iddnom');
            $table->unsignedBigInteger('idnom');
            $table->foreign('idnom')
                  ->references('idnom')
                  ->on('nominas');
            $table->string('concepto_pago');
            $table->float('montopago',14,2);
            $table->string('diast');
            $table->date('fecpag')->format('Y-m-d');
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
        Schema::dropIfExists('pago_nominas');
    }
}
