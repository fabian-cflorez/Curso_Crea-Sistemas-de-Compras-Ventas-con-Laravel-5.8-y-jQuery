<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idventa')->unsigned();
            $table->foreign('idventa')->references('id')->on('ventas')->onDelete('cascade');
            $table->integer('idproducto')->unsigned();
            $table->foreign('idproducto')->references('id')->on('productos');
            $table->integer('cantidad');
            $table->decimal('precio', 11, 2);
            $table->decimal('descuento', 11, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_ventas');
    }
}
