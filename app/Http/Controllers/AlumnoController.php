<?php

namespace App\Http\Controllers;

use App\Alumno;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    /*
        El metodo index listara a todos los alumnos registrados
    */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        /*
            estas variables indican la busqueda y por que criterio se buscara
        */
        $buscar = $request->buscar;
        $criterio = $request->criterio;

        /*
            Si la busqueda es vacia, se listaran todos los alumnos.
            como se indica en el metodo paginate, se listaran los alumnos de 10 en 10
        */

        if ($buscar == '') {

            $alumnos = DB::table('secciones')
            	->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
            	->join('alumnos', 'secciones.id', '=', 'alumnos.seccion_id')
            	->select('secciones.periodo_id', 'periodos.periodo_inicio', 'periodos.periodo_fin', 'secciones.nombre_seccion', 'alumnos.id', 'alumnos.seccion_id',
            		'alumnos.cedula','alumnos.nombre', 
            		'alumnos.apellido', 'alumnos.telefono', 'alumnos.email',
            		'alumnos.dir_ciudad', 'alumnos.dir_avenida', 'alumnos.dir_calle', 'alumnos.dir_casa', 'alumnos.condicion')
                ->orderBy('periodos.periodo_inicio', 'secciones.nombre_seccion', 'alumnos.apellido')
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
                ->orderBy('periodos.periodo_inicio', 'secciones.nombre_seccion', 'alumnos.apellido')
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

    /*
        este metodo busca a un alumno por medio de su cedula
    */
    public function buscarAlumno(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $cedula = $request->cedula;
        $seccion_id = $request->seccion_id;

        $alumno = Alumno::select('id', 'nombre', 'apellido', 'cedula')
            ->where([
                ['seccion_id', '=', $seccion_id],
                ['cedula', '=', $cedula],
                ['condicion', '=', 1]
            ])
            ->take(1)
            ->get();

        return $alumno;
    }

    /*
        se inserta al alumno por medio de los metodos de laravel
        y usando el model Alumno
    */
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

    /*
        se actualiza al alumno por medio de los metodos de laravel
        y usando el model Alumno
    */
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

    /*
        se desactivan y activan alumnos por medio de los metodos de laravel
        y usando el model Alumno
    */
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