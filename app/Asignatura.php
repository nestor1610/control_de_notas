<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    protected $fillable = ['nombre_asignatura'];

    public function secciones()
    {
    	return $this->belongsToMany('App\Seccion', 'asignatura_seccion');
    }
}
