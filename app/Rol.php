<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $fillable = ['nombre', 'descripcion', 'condicion'];
    public $timestamps = false;

    /*
		indicamos una relacion de 1 a muchos con el modelo user
		ya que de esta forma se usaran los metodos de laravel para acceder a 
		los datos relacionadoes
    */
    public function user()
    {
    	return $this->hasMany('App\User');
    }
}
