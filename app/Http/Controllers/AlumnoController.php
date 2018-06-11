<?php

namespace App\Http\Controllers;

use App\Alumno;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {

            $alumnos = DB::table('secciones')
            	->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
            	->join('alumnos', 'secciones.id', '=', 'alumnos.seccion_id')
            	->select('secciones.periodo_id', 'periodos.periodo_inicio', 'periodos.periodo_fin', 'secciones.nombre_seccion', 'alumnos.id', 'alumnos.seccion_id',
            		'alumnos.cedula','alumnos.nombre', 
            		'alumnos.apellido', 'alumnos.telefono', 'alumnos.email',
            		'alumnos.dir_ciudad', 'alumnos.dir_avenida', 'alumnos.dir_calle', 'alumnos.dir_casa', 'alumnos.condicion')
                ->orderBy('periodos.periodo_inicio', 'secciones.nombre_seccion', 'alumnos.nombre')
                ->paginate(10);

        } else {

            $alumnos = DB::table('secciones')
            	->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
            	->join('alumnos', 'secciones.id', '=', 'alumnos.seccion_id')
            	->select('secciones.periodo_id', 'periodos.periodo_inicio', 'periodos.periodo_fin', 'secciones.nombre_seccion', 'alumnos.id', 'alumnos.seccion_id',
            		'alumnos.cedula','alumnos.nombre', 
            		'alumnos.apellido', 'alumnos.telefono', 'alumnos.email',
            		'alumnos.dir_ciudad', 'alumnos.dir_avenida', 'alumnos.dir_calle', 'alumnos.dir_casa', 'alumnos.condicion')
            	->where($criterio, 'like', '%'.$buscar.'%')
                ->orderBy('periodos.periodo_inicio', 'secciones.nombre_seccion', 'alumnos.nombre')
                ->paginate(10);

        }

        return [
            'pagination' => [
                'total' => $alumnos->total(),
                'current_page' => $alumnos->currentPage(),
                'per_page' => $alumnos->perPage(),
                'last_page' => $alumnos->lastPage(),
                'from' => $alumnos->firstItem(),
                'to' => $alumnos->lastItem()
            ],
            'alumnos' => $alumnos
        ];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $alumno = new Alumno();
        $alumno->seccion_id = $request->seccion_id;
        $alumno->cedula = $request->cedula;
        $alumno->nombre = $request->nombre;
        $alumno->apellido = $request->apellido;
        $alumno->email = $request->email;
        $alumno->telefono = $request->telefono;
        $alumno->dir_avenida = $request->dir_avenida;
        $alumno->dir_ciudad = $request->dir_ciudad;
        $alumno->dir_calle = $request->dir_calle;
        $alumno->dir_casa = $request->dir_casa;
        $alumno->condicion = 1;
        $alumno->save();
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $alumno = Alumno::findOrFail( $request->id );
        $alumno->seccion_id = $request->seccion_id;
        $alumno->cedula = $request->cedula;
        $alumno->nombre = $request->nombre;
        $alumno->apellido = $request->apellido;
        $alumno->email = $request->email;
        $alumno->telefono = $request->telefono;
        $alumno->dir_avenida = $request->dir_avenida;
        $alumno->dir_ciudad = $request->dir_ciudad;
        $alumno->dir_calle = $request->dir_calle;
        $alumno->dir_casa = $request->dir_casa;
        $alumno->condicion = 1;
        $alumno->save();
    }

    public function activate(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $alumno = Alumno::findOrFail( $request->id );
        $alumno->condicion = 1;
        $alumno->save();
    }

    public function desactive(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $alumno = Alumno::findOrFail( $request->id );
        $alumno->condicion = 0;
        $alumno->save();
    }
}
