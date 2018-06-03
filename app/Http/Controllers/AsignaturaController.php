<?php

namespace App\Http\Controllers;

use App\Asignatura;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AsignaturaController extends Controller
{
    public function index(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {

            $asignaturas =  Asignatura::orderBy('nombre_asignatura')->paginate(10);

            foreach ($asignaturas as $key => $asignatura) {
            	$asignatura->secciones;
            }

        } else {

            $asignaturas = Asignatura::where($criterio, 'like', '%'.$buscar.'%')
                ->orderBy('nombre_asignatura')
                ->paginate(10);

            foreach ($asignaturas as $key => $asignatura) {
                $asignatura->secciones;
            }

        }

        return [
            'pagination' => [
                'total' => $asignaturas->total(),
                'current_page' => $asignaturas->currentPage(),
                'per_page' => $asignaturas->perPage(),
                'last_page' => $asignaturas->lastPage(),
                'from' => $asignaturas->firstItem(),
                'to' => $asignaturas->lastItem()
            ],
            'asignaturas' => $asignaturas
        ];
    }

    public function store(Request $request)
    {
    	if (!$request->ajax()) return redirect('/');

    	$asignatura = new Asignatura();
		$asignatura->nombre_asignatura = $request->nombre_asignatura;
		$asignatura->save();
    }

    public function registrarSeccion(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $asignatura = Asignatura::findOrFail($request->asignatura_id);
        $asignatura->secciones()->attach($request->seccion_id);
    }

    public function update(Request $request)
    {
    	if (!$request->ajax()) return redirect('/');

    	$asignatura = Asignatura::findOrFail($request->id);
		$asignatura->nombre_asignatura = $request->nombre_asignatura;
		$asignatura->save();
    }

    public function deleteSeccion(Request $request, $asignatura_id, $seccion_id)
    {
        if (!$request->ajax()) return redirect('/');

        $asignatura = Asignatura::findOrFail($asignatura_id);
        $asignatura->secciones()->detach($seccion_id);

    }
}
