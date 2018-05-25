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
Route::post('/periodo/registrar', 'PeriodoController@store');
Route::put('/periodo/actualizar', 'PeriodoController@update');

/* Secciones */
Route::get('/secciones', 'SeccionController@index');
Route::post('/secciones/registrar', 'SeccionController@store');
Route::put('/secciones/actualizar', 'SeccionController@update');