<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'condicion', 'rol_id'
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /*
        indicamos una relacion de 1 a muchos con el modelo rol
        ya que de esta forma se usaran los metodos de laravel para acceder a 
        los datos relacionadoes
    */
    public function rol()
    {
        return $this->belongsTo('App\Rol');
    }
}
