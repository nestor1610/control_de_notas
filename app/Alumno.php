<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
	/*
		Indicamos a que tabla y que columnas de la base de datos seran accedidos por medio del modelo
	*/
    protected $table = 'alumnos';
    protected $fillable = [
    	'seccion_id', 'cedula', 'nombre', 'apellido', 'telefono', 'email', 'dir_ciudad',
    	'dir_avenida', 'dir_calle', 'dir_casa', 'condicion'
    ];

    /*
		indicamos una relacion de 1 a muchos con el modelo secciones
		ya que de esta forma se usaran los metodos de laravel para acceder a 
		los datos relacionadoes
    */
    public function seccion()
    {
    	return $this->belongsTo('App\Seccion', 'seccion_id');
    }

    /*
        indicamos una relacion de muchos a muchos con el modelo alumnos
        ya que de esta forma se usaran los metodos de laravel para acceder a 
        los datos relacionadoes
    */
    public function notas()
    {
        return $this->belongsToMany('App\Nota');
    }
}
