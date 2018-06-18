<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlumnoNotaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumno_nota', function (Blueprint $table) {
            $table->unsignedInteger('alumno_id');
            $table->foreign('alumno_id')->references('id')->on('alumnos');

            $table->unsignedInteger('nota_id');
            $table->foreign('nota_id')->references('id')->on('notas');
            
            $table->primary(['alumno_id', 'nota_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumno_nota');
    }
}
