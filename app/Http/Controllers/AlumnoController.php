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
            	->select('secciones.periodo_id', 'periodos.periodo_inicio', 'periodos.periodo_fin', 'secciones.nombre_seccion',
                    'secciones.ano', 'alumnos.id', 'alumnos.seccion_id', 'alumnos.tipo_documento', 'alumnos.cedula', 'alumnos.nombre',
                    'alumnos.apellido', 'alumnos.cod_telefono', 'alumnos.telefono', 'alumnos.email', 'alumnos.dir_ciudad', 'alumnos.dir_avenida',
                    'alumnos.dir_calle', 'alumnos.dir_casa', 'alumnos.condicion')
                ->orderBy('periodos.periodo_inicio', 'secciones.ano', 'secciones.nombre_seccion', 'alumnos.apellido')
                ->paginate(10);

        } else {

            $alumnos = DB::table('secciones')
            	->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
            	->join('alumnos', 'secciones.id', '=', 'alumnos.seccion_id')
            	->select('secciones.periodo_id', 'periodos.periodo_inicio', 'periodos.periodo_fin', 'secciones.nombre_seccion',
                    'secciones.ano', 'alumnos.id', 'alumnos.seccion_id', 'alumnos.tipo_documento', 'alumnos.cedula','alumnos.nombre',
                    'alumnos.apellido', 'alumnos.cod_telefono', 'alumnos.telefono', 'alumnos.email', 'alumnos.dir_ciudad', 'alumnos.dir_avenida',
                    'alumnos.dir_calle', 'alumnos.dir_casa', 'alumnos.condicion')
            	->where($criterio, 'like', '%'.$buscar.'%')
                ->orderBy('periodos.periodo_inicio', 'secciones.ano', 'secciones.nombre_seccion', 'alumnos.apellido')
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

    public function alumnosSeccion(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $seccion_id = $request->seccion_id;

        $alumnos = DB::table('secciones')
            ->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
            ->join('alumnos', 'secciones.id', '=', 'alumnos.seccion_id')
            ->select('secciones.periodo_id', 'periodos.periodo_inicio', 'periodos.periodo_fin', 'secciones.nombre_seccion',
                'secciones.ano', 'alumnos.id', 'alumnos.seccion_id',  'alumnos.tipo_documento', 'alumnos.cedula','alumnos.nombre',
                'alumnos.apellido', 'alumnos.cod_telefono', 'alumnos.telefono', 'alumnos.email', 'alumnos.dir_ciudad', 'alumnos.dir_avenida',
                'alumnos.dir_calle', 'alumnos.dir_casa', 'alumnos.condicion')
            ->where('alumnos.seccion_id', $seccion_id)
            ->orderBy('periodos.periodo_inicio', 'secciones.ano', 'secciones.nombre_seccion', 'alumnos.apellido')
            ->get();

        return $alumnos;
    }

    /*
        este metodo busca a un alumno por medio de su cedula
    */
    public function buscarAlumno(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $cedula = $request->cedula;
        $seccion_id = $request->seccion_id;
        $asignatura_id = $request->asignatura_id;
        $lapso = $request->lapso;

        $nota = DB::select(
            'SELECT n.nota as existe FROM notas n
            JOIN alumno_nota a_n ON n.id = a_n.nota_id AND n.lapso = :lapso
            JOIN asignatura_nota asig_n ON n.id = asig_n.nota_id
            JOIN asignaturas asig ON asig.id = asig_n.asignatura_id AND asig.id = :asignatura_id
            JOIN alumnos a ON a.id = a_n.alumno_id AND a.cedula = :cedula AND a.seccion_id = :seccion_id', [
                'cedula' => $cedula,
                'asignatura_id' => $asignatura_id,
                'seccion_id' => $seccion_id,
                'lapso' => $lapso
            ]
        );

        if ( $nota != NULL ) return 1;

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
        $alumno->tipo_documento = $request->tipo_documento;
        $alumno->cedula = $request->cedula;
        $alumno->nombre = $request->nombre;
        $alumno->apellido = $request->apellido;
        $alumno->email = $request->email;
        $alumno->cod_telefono = $request->cod_telefono;
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
        $alumno->tipo_documento = $request->tipo_documento;
        $alumno->cedula = $request->cedula;
        $alumno->nombre = $request->nombre;
        $alumno->apellido = $request->apellido;
        $alumno->email = $request->email;
        $alumno->cod_telefono = $request->cod_telefono;
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

    public function seccionPdf(Request $request)
    {
        $seccion_id = $request->seccion_id;

        $alumnos = DB::table('secciones')
            ->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
            ->join('alumnos', 'secciones.id', '=', 'alumnos.seccion_id')
            ->select('secciones.periodo_id', 'periodos.periodo_inicio', 'periodos.periodo_fin', 'secciones.nombre_seccion',
                'secciones.ano', 'alumnos.id', 'alumnos.seccion_id', 'alumnos.tipo_documento', 'alumnos.cedula','alumnos.nombre',
                'alumnos.apellido', 'alumnos.cod_telefono', 'alumnos.cod_telefono', 'alumnos.telefono', 'alumnos.email', 'alumnos.dir_ciudad', 'alumnos.dir_avenida',
                'alumnos.dir_calle', 'alumnos.dir_casa', 'alumnos.condicion')
            ->where('alumnos.seccion_id', $seccion_id)
            ->orderBy('periodos.periodo_inicio', 'secciones.ano', 'secciones.nombre_seccion', 'alumnos.apellido')
            ->get();

            $seccion = DB::table('secciones')
            ->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
            ->select('secciones.nombre_seccion', 'secciones.ano', 'periodos.periodo_inicio', 'periodos.periodo_fin')
            ->where('secciones.id', $seccion_id)
            ->first();

        $alumnos = ( count($alumnos) > 0 ) ? $alumnos : [];

        $pdf = \PDF::loadView('pdf.seccionpdf', [
            'alumnos' => $alumnos,
            'seccion' => $seccion
        ]);

        return $pdf->download($seccion->nombre_seccion.'_'.$seccion->ano.'_'.$seccion->periodo_inicio.'-'.$seccion->periodo_fin.'.pdf');

    }

    public function citacionPdf(Request $request)
    {
        $alumno = DB::table('secciones')
        ->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
        ->join('alumnos', 'secciones.id', '=', 'alumnos.seccion_id')
        ->select('secciones.periodo_id', 'periodos.periodo_inicio', 'periodos.periodo_fin', 'secciones.nombre_seccion',
            'secciones.ano', 'alumnos.id', 'alumnos.seccion_id', 'alumnos.tipo_documento', 'alumnos.cedula','alumnos.nombre',
            'alumnos.apellido', 'alumnos.cod_telefono', 'alumnos.cod_telefono', 'alumnos.telefono', 'alumnos.email', 'alumnos.dir_ciudad', 'alumnos.dir_avenida',
            'alumnos.dir_calle', 'alumnos.dir_casa', 'alumnos.condicion')
        ->where([
                ['alumnos.id', '=', $request->alumno_id],
                ['alumnos.condicion', '=', 1]
            ])
        ->first();

        $pdf = \PDF::loadView('pdf.citacionpdf', [
            'alumno' => $alumno
        ]);

        return $pdf->download($alumno->cedula.'citacion.pdf');
    }
}