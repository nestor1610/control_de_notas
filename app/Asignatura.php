<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    protected $fillable = ['nombre_asignatura'];

    /*
		indicamos una relacion de muchos a muchos con el modelo secciones
		ya que de esta forma se usaran los metodos de laravel para acceder a 
		los datos relacionadoes
    */
    public function secciones()
    {
    	return $this->belongsToMany('App\Seccion', 'asignatura_seccion');
    }
}
