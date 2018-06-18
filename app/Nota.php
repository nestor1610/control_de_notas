<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $fillable = ['nota', 'lapso'];

    /*
		indicamos una relacion de muchos a muchos con el modelo alumnos
		ya que de esta forma se usaran los metodos de laravel para acceder a 
		los datos relacionadoes
    */
    public function alumnos()
    {
    	return $this->belongsToMany('App\Alumno');
    }

    /*
		indicamos una relacion de muchos a muchos con el modelo asignaturas
		ya que de esta forma se usaran los metodos de laravel para acceder a 
		los datos relacionadoes
    */
    public function asignaturas()
    {
    	return $this->belongsToMany('App\Asignatura');
    }
}
