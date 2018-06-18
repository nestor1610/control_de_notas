<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsignaturaNotaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignatura_nota', function (Blueprint $table) {
            $table->unsignedInteger('asignatura_id');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas');

            $table->unsignedInteger('nota_id');
            $table->foreign('nota_id')->references('id')->on('notas');
            
            $table->primary(['asignatura_id', 'nota_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asignatura_nota');
    }
}
