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

Route::get('/', function () {
    return view('contenido.contenido');
});

/* Periodos */
Route::get('/periodo', 'PeriodoController@index');
Route::get('/periodo/listar-periodos', 'PeriodoController@listarPeriodos');
Route::post('/periodo/registrar', 'PeriodoController@store');
Route::put('/periodo/actualizar', 'PeriodoController@update');

/* Secciones */
Route::get('/seccion', 'SeccionController@index');
Route::post('/seccion/registrar', 'SeccionController@store');
Route::put('/seccion/actualizar', 'SeccionController@update');

/* Asignaturas */
Route::get('/asignatura', 'AsignaturaController@index');
Route::post('/asignatura/registrar', 'AsignaturaController@store');
Route::put('/asignatura/actualizar', 'AsignaturaController@update');