<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'secciones';
    protected $fillable = ['nombre_seccion', 'ano', 'periodo_id'];

    /*
		indicamos una relacion de 1 a muchos con el modelo periodo
		ya que de esta forma se usaran los metodos de laravel para acceder a 
		los datos relacionadoes
    */
    public function periodo()
	{
		return $this->belongsTo('App\Periodo');
	}

	/*
		indicamos una relacion de muchos a muchos con el modelo asignaturas
		ya que de esta forma se usaran los metodos de laravel para acceder a 
		los datos relacionadoes
    */
	public function asignaturas()
	{
		return $this->belongsToMany('App\Asignatura', 'asignatura_seccion');
	}
}
