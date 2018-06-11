<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rol;

class RolController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '')
            $roles =  Rol::orderBy('id', 'desc')->get();
        else
            $roles = Rol::where($criterio, 'like', '%'. $buscar . '%')->orderBy('id', 'desc')->get();

        return $roles;
    }

    public function listarRoles(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $roles = Rol::where('condicion', 1)
            ->select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();

        return $roles;
    }
}
