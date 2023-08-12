<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTotalPagoNominasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('total_pago_nominas', function (Blueprint $table) {
            $table->id('idtnom');
            $table->unsignedBigInteger('idnom');
            $table->foreign('idnom')
                    ->references('idnom')
                    ->on('nominas');
            $table->unsignedBigInteger('idasi');
            $table->foreign('idasi')
                    ->references('idasi')
                    ->on('asientos');
            $table->float('totalasignacion',14,2);
            $table->float('totaldeduccion',14,2);
            $table->float('netocobrar',14,2);
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
        Schema::dropIfExists('total_pago_nominas');
    }
}
