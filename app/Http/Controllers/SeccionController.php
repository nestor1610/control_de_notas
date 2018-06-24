<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seccion;
use Illuminate\Support\Facades\DB;

class SeccionController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {

            $secciones =  Seccion::orderBy('ano', 'asc')->paginate(10);

            foreach ($secciones as $key => $seccion) {
            	$seccion->periodo->orderBy('periodo_inicio');
            }

        } else {

            $secciones = Seccion::where($criterio, 'like', '%'.$buscar.'%')
            	->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
            	->select('secciones.id', 'secciones.nombre_seccion', 'secciones.ano', 'secciones.periodo_id', 'secciones.created_at', 'secciones.updated_at')
                ->orderBy('ano', 'asc')
                ->paginate(10);

            foreach ($secciones as $key => $seccion) {
                $seccion->periodo->orderBy('periodo_inicio');
            }

        }

        return [
            'pagination' => [
                'total' => $secciones->total(),
                'current_page' => $secciones->currentPage(),
                'per_page' => $secciones->perPage(),
                'last_page' => $secciones->lastPage(),
                'from' => $secciones->firstItem(),
                'to' => $secciones->lastItem()
            ],
            'secciones' => $secciones
        ];
    }

    public function listarSecciones(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $secciones = DB::select('select secciones.id, secciones.nombre_seccion, secciones.ano
            from secciones
            where id NOT IN (select seccion_id from asignatura_seccion where asignatura_id = :asignatura_id)
            order by ano;', [
                'asignatura_id' => $request->asignatura_id
            ]);

        return $secciones;
    }

    public function listarSeccionesAlumno(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $secciones = Seccion::select('secciones.id', 'nombre_seccion', 'ano', 'periodos.periodo_inicio',
            'periodos.periodo_fin')
            ->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
            ->where('periodos.id', $request->periodo_id)
            ->orderBy('periodo_inicio', 'desc')->get();

        return $secciones;
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $seccion = new Seccion();
        $seccion->nombre_seccion = $request->nombre_seccion;
        $seccion->ano = $request->ano;
        $seccion->periodo_id = $request->periodo_id;
        $seccion->save();
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $seccion = Seccion::find( $request->id );
        $seccion->nombre_seccion = $request->nombre_seccion;
        $seccion->ano = $request->ano;
        $seccion->periodo_id = $request->periodo_id;
        $seccion->save();
    }
}
