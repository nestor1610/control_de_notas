<?php

namespace App\Http\Controllers;
use App\Periodo;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '')
            $periodos =  Periodo::orderBy('id', 'desc')->get();
        else
            $periodos = Periodo::where($criterio, 'like', '%'. $buscar . '%')->orderBy('id', 'desc')->get();

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
