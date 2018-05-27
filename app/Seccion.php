<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'secciones';
    protected $fillable = ['nombre_seccion', 'periodo_id'];

    public function periodo()
	{
		return $this->belongsTo('App\Periodo');
	}

	public function asignaturas()
	{
		return $this->belongsToMany('App\Asignatura', 'asignatura_seccion');
	}
}
