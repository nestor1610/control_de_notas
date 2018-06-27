<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {

            $users =  User::orderBy('id', 'desc')->get();

            foreach ($users as $key => $user) {
            	$user->rol;
            }

        } else {

            $users = User::where($criterio, 'like', '%'. $buscar . '%')->orderBy('id', 'desc')->get();

            foreach ($users as $key => $user) {
            	$user->rol;
            }

        }

        return $users;
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $user = new User();
        $user->rol_id = $request->rol_id;
        $user->email = $request->email;
        $user->password = bcrypt( $request->password );
        $user->condicion = 1;
        $user->save();
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $user = User::find( $request->id );
        $user->rol_id = $request->rol_id;
        $user->email = $request->email;
        $user->password = bcrypt( $request->password );
        $user->save();
    }

    public function delete(Request $request, $usuario_id)
    {
        if (!$request->ajax()) return redirect('/');

        if ($usuario_id == Auth::user()->id)
            return 0;

        $user = User::findOrFail($usuario_id);
        $user->delete();

        return 1;

    }
}
