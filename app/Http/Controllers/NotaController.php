<?php

namespace App\Http\Controllers;

use App\Asignatura;
use App\Nota;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    public function index(Request $request)
    {
    	if (!$request->ajax()) return redirect('/');

        $seccion_id = $request->seccion_id;
        $asignatura_id = $request->asignatura_id;
        	
    	$notas = DB::table('alumno_nota')
    		->join('alumnos', 'alumno_nota.alumno_id', '=', 'alumnos.id')
    		->join('notas', 'alumno_nota.nota_id', '=', 'notas.id')
    		->join('asignatura_nota', 'notas.id', '=', 'asignatura_nota.nota_id')
    		->join('asignaturas', 'asignatura_nota.asignatura_id', '=', 'asignaturas.id')
    		->select( DB::raw('avg( notas.nota ) as promedio'), 'asignaturas.nombre_asignatura', 'alumnos.cedula', 'alumnos.apellido', 'alumnos.nombre')
    		->groupBy('alumnos.cedula', 'asignaturas.nombre_asignatura', 'alumnos.apellido', 'alumnos.nombre')
    		->where([
    			['alumnos.seccion_id', '=', $seccion_id],
    			['asignaturas.id', '=', $asignatura_id],
    			['alumnos.condicion', '=', 1]
    		])
    		->orderBy('alumnos.cedula')
    		->paginate(10);

    	return [
            'pagination' => [
                'total' => $notas->total(),
                'current_page' => $notas->currentPage(),
                'per_page' => $notas->perPage(),
                'last_page' => $notas->lastPage(),
                'from' => $notas->firstItem(),
                'to' => $notas->lastItem()
            ],
            'notas' => $notas
        ];
    }

    public function store(Request $request)
    {
    	if (!$request->ajax()) return redirect('/');

        try {
        	
        	DB::beginTransaction();

	        foreach ($request->data as $key => $nota_alumno) {
	        	
	        	$nota = new Nota();
	        	$nota->nota = $nota_alumno['nota'];
	        	$nota->lapso = $nota_alumno['lapso'];
	        	$nota->save();

	        	$nota->alumnos()->attach($nota_alumno['alumno_id']);
	        	$nota->asignaturas()->attach($request->asignatura_id);

	        }

            DB::commit();

        } catch (Exception $e) {

        	DB::rollBack();
        	
        }
    }
}
