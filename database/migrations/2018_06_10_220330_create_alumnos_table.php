<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlumnosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('alumnos', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('seccion_id');
			$table->foreign('seccion_id')->references('id')->on('secciones');
			$table->string('tipo_documento', 1);
			$table->unsignedInteger('cedula')->unique();
			$table->string('nombre', 40);
			$table->string('apellido', 40);
			$table->string('email', 40)->unique()->nullable();
			$table->string('cod_telefono', 4)->nullable();
			$table->string('telefono', 35)->nullable();
			$table->string('dir_ciudad', 40)->nullable();
			$table->string('dir_avenida', 40)->nullable();
			$table->string('dir_calle', 40)->nullable();
			$table->string('dir_casa', 40)->nullable();
			$table->boolean('condicion')->default(1);
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
		Schema::dropIfExists('alumnos');
	}
}
