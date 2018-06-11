<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumnos';
    protected $fillable = [
    	'seccion_id', 'cedula', 'nombre', 'apellido', 'telefono', 'email', 'dir_ciudad',
    	'dir_avenida', 'dir_calle', 'dir_casa', 'condicion'
    ];

    public function seccion()
    {
    	return $this->belongsTo('App\Seccion', 'seccion_id');
    }
}
