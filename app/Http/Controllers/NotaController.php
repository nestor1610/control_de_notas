<?php

namespace App\Http\Controllers;

use App\Asignatura;
use App\Nota;
use App\Alumno;
use App\Seccion;
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
                'alumnos.id as alumno_id', 'alumnos.seccion_id', 'alumnos.tipo_documento', 'alumnos.cedula', 'alumnos.apellido', 'alumnos.nombre')
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
            'secciones.ano', 'alumnos.id', 'alumnos.seccion_id', 'alumnos.tipo_documento', 'alumnos.cedula','alumnos.nombre',
            'alumnos.apellido', 'alumnos.cod_telefono', 'alumnos.telefono', 'alumnos.email', 'alumnos.dir_ciudad', 'alumnos.dir_avenida',
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

                    $promedio = ( $nota_final[$index - 1]['primer_lapso'] + 
                        $nota_final[$index - 1]['segundo_lapso'] + $nota_final[$index - 1]['tercer_lapso'] ) / 3;

                    $nota_final[$index - 1]['promedio_asignatura'] = number_format($promedio, 2, ',', '.');

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

    public function alumnosAprobadosPdf(Request $request)
    {
        $alumnos = DB::select(
            'SELECT a.tipo_documento, a.cedula, a.apellido, a.nombre, asig.nombre_asignatura,
                ROUND( ( SUM( n.nota ) / 3 ), 2 ) promedio, IF( ( SUM( n.nota ) / 3 ) > 10, 1, 0 ) estatus
            FROM alumnos a
            JOIN secciones s ON a.seccion_id = s.id
            JOIN alumno_nota alum_not  ON alum_not.alumno_id = a.id
            JOIN notas n ON n.id = alum_not.nota_id
            JOIN asignatura_nota asig_not ON asig_not.nota_id = n.id
            JOIN asignaturas asig ON asig.id = asig_not.asignatura_id
            WHERE s.id = :seccion_id
            GROUP BY a.tipo_documento, a.cedula, a.apellido, a.nombre, asig.nombre_asignatura
            HAVING count(n.nota) = 3 
            ORDER BY a.cedula, asig.nombre_asignatura', [
            'seccion_id' => $request->seccion_id
        ]);

        $seccion = Seccion::where('id', $request->seccion_id)
            ->first();

        $seccion->periodo;

        $total_asignaturas = Seccion::findOrFail($request->seccion_id)
            ->asignaturas()
            ->count();

        $cedula = 0;
        $index = 0;
        $aprobados = [];

        if ( $alumnos != NULL ) {
            
            foreach ($alumnos as $key => $alumno) {
                
                if ( $alumno->cedula != $cedula ) {

                    $aprobados[$index]['tipo_documento'] = $alumno->tipo_documento;
                    $aprobados[$index]['cedula'] = $alumno->cedula;
                    $aprobados[$index]['apellido'] = $alumno->apellido;
                    $aprobados[$index]['nombre'] = $alumno->nombre;
                    $aprobados[$index]['asignaturas'][] = [
                        'nombre' => $alumno->nombre_asignatura,
                        'promedio' => $alumno->promedio
                    ];
                    $aprobados[$index]['aprobadas'] = 0;
                    $aprobados[$index]['reprobadas'] = 0;

                    if ( $alumno->estatus )
                        $aprobados[$index]['aprobadas']++;
                    else
                        $aprobados[$index]['reprobadas']++;

                    if ( $total_asignaturas - 1 <= $aprobados[$index]['aprobadas'] )
                        $aprobados[$index]['estatus'] = 'aprobado';
                    else
                        $aprobados[$index]['estatus'] = 'reprobado';

                    $cedula = $alumno->cedula;
                    $index++;

                } elseif ( $alumno->cedula == $cedula ) {
                    
                    $aprobados[$index - 1]['asignaturas'][] = [
                        'nombre' => $alumno->nombre_asignatura,
                        'promedio' => $alumno->promedio
                    ];

                    if ( $alumno->estatus )
                        $aprobados[$index - 1]['aprobadas']++;
                    else
                        $aprobados[$index - 1]['reprobadas']++;

                    if ( $total_asignaturas - 1 <= $aprobados[$index - 1]['aprobadas'] )
                        $aprobados[$index - 1]['estatus'] = 'aprobado';
                    else
                        $aprobados[$index - 1]['estatus'] = 'reprobado';
                }
            }
        }

        $pdf = \PDF::loadView('pdf.alumnosaprobados', [
            'alumnos' => $aprobados,
            'seccion' => $seccion,
            'total_asignaturas' => $total_asignaturas
        ]);

        return $pdf->download('alumnos_aprobados_'.$seccion->ano.'_'.$seccion->nombre_seccion.'.pdf');

    }
}
