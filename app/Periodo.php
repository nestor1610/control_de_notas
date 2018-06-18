<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $fillable = ['periodo_inicio', 'periodo_fin'];

    /*
		indicamos una relacion de 1 a muchos con el modelo secciones
		ya que de esta forma se usaran los metodos de laravel para acceder a 
		los datos relacionadoes
    */
    public function secciones()
    {
    	return $this->hasMany('App\Seccion');
    }
}
