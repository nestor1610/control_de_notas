<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $fillable = ['periodo_inicio', 'periodo_fin'];

    public function secciones()
    {
    	return $this->hasMany('App\Seccion');
    }
}
