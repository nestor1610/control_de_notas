<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
	Estas rutas solo podran ser accedidas por los usuarios que no se han logeado
*/
Route::group( ['middleware' => ['guest'] ], function () {

	Route::get('/', 'Auth\LoginController@showLoginForm');
	Route::post('/login', 'Auth\LoginController@login')->name('login');	

});

/*
	Estas rutas solo podran ser accedidas por los usuarios que se han logeado
	y aparte tienen el rol Administrador
*/

Route::group( ['middleware' => ['auth'] ], function () {

	Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

	Route::group( ['middleware' => ['Administrador'] ], function () {

		Route::get('/main', function () {
		    return view('contenido.contenido');
		})->name('main');

		/* Periodos */
		Route::get('/periodo', 'PeriodoController@index');
		Route::get('/periodo/listar-periodos', 'PeriodoController@listarPeriodos');
		Route::post('/periodo/registrar', 'PeriodoController@store');
		Route::put('/periodo/actualizar', 'PeriodoController@update');

		/* Secciones */
		Route::get('/seccion', 'SeccionController@index');
		Route::get('/seccion/listar-seccion', 'SeccionController@listarSecciones');
		Route::get('/seccion/listar-seccion-alumno', 'SeccionController@listarSeccionesAlumno');
		Route::get('/seccion/pdf/asignatura', 'SeccionController@asignaturasSeccionPdf');
		Route::get('/seccion/pdf/notas', 'SeccionController@notasSeccionPdf');
		Route::post('/seccion/registrar', 'SeccionController@store');
		Route::put('/seccion/actualizar', 'SeccionController@update');

		/* Asignaturas */
		Route::get('/asignatura', 'AsignaturaController@index');
		Route::get('/asignatura/listar-asignatura', 'AsignaturaController@listarAsignatura');
		Route::post('/asignatura/registrar', 'AsignaturaController@store');
		Route::post('/asignatura/registrar/seccion', 'AsignaturaController@registrarSeccion');
		Route::put('/asignatura/actualizar', 'AsignaturaController@update');
		Route::delete('/asignatura/eliminar/{asignatura}/{seccion}', 'AsignaturaController@deleteSeccion');

		/* Alumnos */
		Route::get('/alumno', 'AlumnoController@index');
		Route::get('/alumno/seccion', 'AlumnoController@alumnosSeccion');
		Route::get('/alumno/buscar-alumno', 'AlumnoController@buscarAlumno');
		Route::get('/alumno/listar-alumno', 'AlumnoController@listarAlumnos');
		Route::post('/alumno/registrar', 'AlumnoController@store');
		Route::put('/alumno/actualizar', 'AlumnoController@update');
		Route::put('/alumno/activar', 'AlumnoController@activate');
		Route::put('/alumno/desactivar', 'AlumnoController@desactive');
		Route::get('/alumno/pdf/seccion', 'AlumnoController@seccionPdf');

		/* Notas */
		Route::get('/nota', 'NotaController@index');
		Route::post('/nota/registrar', 'NotaController@store');
		Route::put('/nota/actualizar', 'NotaController@update');
		Route::get('/nota/pdf/alumno', 'NotaController@listarPdf');
		Route::get('/nota/pdf/aprobados', 'NotaController@alumnosAprobadosPdf');

		/* Roles */
		Route::get('/rol', 'RolController@index');
		Route::get('/rol/listar-roles', 'RolController@listarRoles');

		/* Usuarios */
		Route::get('/user', 'UserController@index');
		Route::post('/user/registrar', 'UserController@store');
		Route::put('/user/actualizar', 'UserController@update');
		Route::delete('/user/delete/{usuario_id}', 'userController@delete');

	});

});

//Route::get('/home', 'HomeController@index')->name('home');
