<?php

namespace App\Http\Controllers;

use App\Periodo;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $periodo_inicio = $request->periodo_inicio;
        $periodo_fin = $request->periodo_fin;
        //$criterio = $request->criterio;

        if ($periodo_inicio == 0 && $periodo_fin == 0)
            $periodos =  Periodo::orderBy('periodo_inicio', 'desc')->get();
        else {
            $periodos = Periodo::where([
                ['periodo_inicio', 'like', $periodo_inicio],
                ['periodo_fin', 'like', $periodo_fin]
            ])->orderBy('id', 'desc')->get();
        }

        return $periodos;
    }

    public function listarPeriodos(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $periodos = Periodo::select('id','periodo_inicio', 'periodo_fin')
            ->orderBy('periodo_inicio', 'desc')
            ->get();

        return $periodos;
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $periodo = new Periodo();
        $periodo->periodo_inicio = $request->periodo_inicio;
        $periodo->periodo_fin = $request->periodo_fin;
        $periodo->save();
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $periodo = Periodo::find( $request->id );
        $periodo->periodo_inicio = $request->periodo_inicio;
        $periodo->periodo_fin = $request->periodo_fin;
        $periodo->save();
    }
}
