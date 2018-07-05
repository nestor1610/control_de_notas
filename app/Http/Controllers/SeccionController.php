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

    public function asignaturasSeccionPdf(Request $request)
    {
        $seccion = Seccion::findOrfail( $request->id );
        $seccion->asignaturas;
        $seccion->periodo;

        $pdf = \PDF::loadView('pdf.seccionasignaturapdf', [
            'seccion' => $seccion
        ]);

        return $pdf->download($seccion->ano.'_'.$seccion->nombre_seccion.'.pdf');
    }

    public function notasSeccionPdf(Request $request)
    {
        $notas = DB::select('SELECT a.cedula, a.apellido, a.nombre,
				asig.nombre_asignatura,
				n.lapso, n.nota
			FROM alumnos a
			LEFT JOIN alumno_nota alum_nota ON a.id = alum_nota.alumno_id
			JOIN secciones s ON a.seccion_id = s.id AND s.id = :seccion_id
			LEFT JOIN notas n ON n.id = alum_nota.nota_id
			LEFT JOIN asignatura_nota asig_not ON asig_not.nota_id = n.id
			LEFT JOIN asignaturas asig ON asig.id = asig_not.asignatura_id
			GROUP BY a.cedula, a.apellido, a.nombre, asig.nombre_asignatura, n.lapso, n.nota
			ORDER BY a.cedula, a.apellido, a.nombre, asig.nombre_asignatura, n.lapso', [
			'seccion_id' => $request->seccion_id
		]);

		$seccion = Seccion::where('id', $request->seccion_id)->first();

		$asignaturas = Seccion::findOrFail($request->seccion_id)
			->asignaturas()
			->orderBy('nombre_asignatura')
			->get();

		$nombre_asignaturas = [];

		foreach ($asignaturas as $key => $asignatura) {
			$nombre_asignaturas[$asignatura->nombre_asignatura] = [
				1 => 0,
				2 => 0,
				3 => 0,
				'promedio' => 0
			];
		}

		$cedula = 0;
        $index = 0;
		$notas_seccion = [];

		if ( $notas != NULL ) {
			
			foreach ($notas as $key => $nota) {
				
				if ( $nota->cedula != $cedula ) {
					
					$notas_seccion[$index]['cedula'] = $nota->cedula;
					$notas_seccion[$index]['alumno'] = $nota->apellido.' '.$nota->nombre;
					$notas_seccion[$index]['asignatura_notas'] = $nombre_asignaturas;
					if ( $nota->nombre_asignatura == NULL ){
						$cedula = $nota->cedula;
						$index++;
						continue;
					};
					$notas_seccion[$index]['asignatura_notas'][$nota->nombre_asignatura][$nota->lapso] = $nota->nota;
					$cedula = $nota->cedula;
					$index++;

				} elseif ( $nota->cedula == $cedula ) {
					
					$notas_seccion[$index - 1]['asignatura_notas'][$nota->nombre_asignatura][$nota->lapso] = $nota->nota;
				}

				$promedio = (
					$notas_seccion[$index - 1]['asignatura_notas'][$nota->nombre_asignatura][1] +
					$notas_seccion[$index - 1]['asignatura_notas'][$nota->nombre_asignatura][2] +
					$notas_seccion[$index - 1]['asignatura_notas'][$nota->nombre_asignatura][3]
				) / 3;
				$notas_seccion[$index - 1]['asignatura_notas'][$nota->nombre_asignatura]['promedio'] =
					number_format($promedio, 2, ',', '.');
			}
		}

		$pdf = \PDF::loadView('pdf.notasseccion', [
            'alumnos' => $notas_seccion,
            'seccion' => $seccion,
            'asignaturas' => $nombre_asignaturas
        ]);

        return $pdf->download('notas_seccion_'.$seccion->ano.'_'.$seccion->nombre_seccion.'.pdf');
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
