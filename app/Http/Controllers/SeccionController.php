<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seccion;
use Illuminate\Support\Facades\DB;

class SeccionController extends Controller
{
    public function index(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {

            $secciones =  Seccion::orderBy('nombre_seccion', 'asc')->paginate(10);

            foreach ($secciones as $key => $seccion) {
            	$seccion->periodo->orderBy('periodo_inicio');
            }

        } else {

            $secciones = Seccion::where($criterio, 'like', '%'.$buscar.'%')
            	->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
            	->select('secciones.id', 'secciones.nombre_seccion', 'secciones.periodo_id', 'secciones.created_at', 'secciones.updated_at')
                ->orderBy('nombre_seccion', 'asc')
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
        //if (!$request->ajax()) return redirect('/');

        $secciones = Seccion::select('secciones.id','nombre_seccion')
            ->where('id', '!=', $request->id)
            ->orderBy('nombre_seccion')
            ->get();

        return $secciones;
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $seccion = new Seccion();
        $seccion->nombre_seccion = $request->nombre_seccion;
        $seccion->periodo_id = $request->periodo_id;
        $seccion->save();
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $seccion = Seccion::find( $request->id );
        $seccion->nombre_seccion = $request->nombre_seccion;
        $seccion->periodo_id = $request->periodo_id;
        $seccion->save();
    }
}
