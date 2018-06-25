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
        $lapso = $request->lapso;
        	
    	$notas = DB::table('alumno_nota')
    		->join('alumnos', 'alumno_nota.alumno_id', '=', 'alumnos.id')
    		->join('notas', 'alumno_nota.nota_id', '=', 'notas.id')
    		->join('asignatura_nota', 'notas.id', '=', 'asignatura_nota.nota_id')
    		->join('asignaturas', 'asignatura_nota.asignatura_id', '=', 'asignaturas.id')
    		->select( 'notas.id', 'notas.nota', 'notas.lapso', 'asignaturas.nombre_asignatura',
                'alumnos.id as alumno_id', 'alumnos.seccion_id', 'alumnos.cedula', 'alumnos.apellido', 'alumnos.nombre')
    		->where([
    			['alumnos.seccion_id', '=', $seccion_id],
    			['asignaturas.id', '=', $asignatura_id],
    			['alumnos.condicion', '=', 1],
                ['notas.lapso', '=', $lapso]
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

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $nota = Nota::findOrFail($request->nota_id);
        $nota->nota = $request->nota;
        $nota->save();
    }

    public function listarPdf(Request $request)
    {
        $alumno = DB::table('secciones')
        ->join('periodos', 'secciones.periodo_id', '=', 'periodos.id')
        ->join('alumnos', 'secciones.id', '=', 'alumnos.seccion_id')
        ->select('secciones.periodo_id', 'periodos.periodo_inicio', 'periodos.periodo_fin', 'secciones.nombre_seccion',
            'secciones.ano', 'alumnos.id', 'alumnos.seccion_id', 'alumnos.cedula','alumnos.nombre',
            'alumnos.apellido', 'alumnos.telefono', 'alumnos.email', 'alumnos.dir_ciudad', 'alumnos.dir_avenida',
            'alumnos.dir_calle', 'alumnos.dir_casa', 'alumnos.condicion')
        ->where([
                ['alumnos.seccion_id', '=', $request->seccion_id],
                ['alumnos.id', '=', $request->alumno_id],
                ['alumnos.condicion', '=', 1]
            ])
        ->first();

        $notas = DB::table('alumno_nota')
            ->join('alumnos', 'alumno_nota.alumno_id', '=', 'alumnos.id')
            ->join('notas', 'alumno_nota.nota_id', '=', 'notas.id')
            ->join('asignatura_nota', 'notas.id', '=', 'asignatura_nota.nota_id')
            ->join('asignaturas', 'asignatura_nota.asignatura_id', '=', 'asignaturas.id')
            ->select( 'notas.nota', 'notas.lapso', 'asignaturas.nombre_asignatura')
            ->where([
                ['alumnos.seccion_id', '=', $request->seccion_id],
                ['alumnos.id', '=', $request->alumno_id],
                ['alumnos.condicion', '=', 1]
            ])
            ->orderBy('asignaturas.nombre_asignatura')
            ->get();

        $asignatura = '';
        $index = 0;
        $nota_final = [];

        if ($notas != NULL) {
            
            foreach ($notas as $key => $nota) {
                
                if ( $nota->nombre_asignatura != $asignatura ) {
                    
                    $nota_final[$index]['asignatura'] = $nota->nombre_asignatura;
                    $nota_final[$index]['primer_lapso'] = '';
                    $nota_final[$index]['segundo_lapso'] = '';
                    $nota_final[$index]['tercer_lapso'] = '';

                    switch ($nota->lapso) {
                        case 1:
                            $nota_final[$index]['primer_lapso'] = $nota->nota;
                        break;

                        case 2:
                            $nota_final[$index]['segundo_lapso'] = $nota->nota;
                        break;

                        case 3:
                            $nota_final[$index]['tercer_lapso'] = $nota->nota;
                        break;
                    }

                    $asignatura = $nota->nombre_asignatura;
                    $index++;

                } elseif ( $nota->nombre_asignatura == $asignatura ) {
                
                    switch ($nota->lapso) {
                        case 1:
                            $nota_final[$index - 1]['primer_lapso'] = $nota->nota;
                        break;

                        case 2:
                            $nota_final[$index - 1]['segundo_lapso'] = $nota->nota;
                        break;

                        case 3:
                            $nota_final[$index - 1]['tercer_lapso'] = $nota->nota;
                        break;
                    }

                }

                if ( $nota_final[$index - 1]['primer_lapso'] != '' &&
                    $nota_final[$index - 1]['segundo_lapso'] != '' &&
                    $nota_final[$index - 1]['tercer_lapso'] != '' ) {

                    $nota_final[$index - 1]['promedio_asignatura'] = ( $nota_final[$index - 1]['primer_lapso'] + 
                        $nota_final[$index - 1]['segundo_lapso'] + $nota_final[$index - 1]['tercer_lapso'] ) / 3;

                } else {
                    $nota_final[$index - 1]['promedio_asignatura'] = '';
                }
            }
        }

        $nombre_archivo = $alumno->ano.'_'. $alumno->nombre_seccion.'_'.$alumno->cedula.
            '_'.$alumno->periodo_inicio.'-'.$alumno->periodo_fin.'.pdf';

        $pdf = \PDF::loadView('pdf.alumnopdf', [
            'alumno' => $alumno,
            'notas' => $nota_final
        ]);

        return $pdf->download($nombre_archivo);
    }
}
