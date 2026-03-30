<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('login', [App\Http\Controllers\UserController::class, 'login']);
Route::get('licencias/verificar/{codigo}', [App\Http\Controllers\LicenciaController::class, 'verifyApi']);

Route::post('formulario',[\App\Http\Controllers\FormularioController::class,'store']); 
Route::post('searchpersona/',[\App\Http\Controllers\PersonaController::class,'searchpersona']); 
Route::get('generatePdf/{id}',[\App\Http\Controllers\FormularioController::class,'generatePdf']); 
Route::get('delito', [App\Http\Controllers\DelitoController::class,'index']);


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('me', [App\Http\Controllers\UserController::class, 'me']);
    Route::post('logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::post('cambioEstado/{id}', [App\Http\Controllers\UserController::class, 'cambioEstado']);
    Route::apiResource('user', App\Http\Controllers\UserController::class);
    Route::apiResource('propietario', App\Http\Controllers\PropietarioController::class);
    Route::apiResource('inspection', App\Http\Controllers\InspectionController::class);
    Route::apiResource('vehicle', App\Http\Controllers\VehicleController::class);
    Route::get('listTipo', [App\Http\Controllers\VehicleController::class,'listTipo']);
    Route::get('listMarca', [App\Http\Controllers\VehicleController::class,'listMarca']);
    Route::get('listModelo', [App\Http\Controllers\VehicleController::class,'listModelo']);
    Route::get('listLinea', [App\Http\Controllers\VehicleController::class,'listLinea']);
    Route::get('listInsp/{fecha}', [App\Http\Controllers\InspectionController::class,'listInsp']);
    Route::post('reportFecha',[\App\Http\Controllers\FormularioController::class,'reportFecha']); 
    Route::post('actualizar',[\App\Http\Controllers\InspectionController::class,'actualizar']); 
    Route::post('actualizarPosicion',[\App\Http\Controllers\UserController::class,'actualizarPosicion']);     
    Route::get('listPosicion',[\App\Http\Controllers\UserController::class,'listPosicion']);     

    Route::apiResource('licencia', App\Http\Controllers\LicenciaController::class);
    Route::post('licencia/{licencia}/anular', [App\Http\Controllers\LicenciaController::class, 'anular']);
    Route::post('licencia/{licencia}/renovar', [App\Http\Controllers\LicenciaController::class, 'renovar']);
    Route::get('licencia/{licencia}/credencial', [App\Http\Controllers\LicenciaController::class, 'credencialPdf']);
    Route::get('licencia/{licencia}/pdf', [App\Http\Controllers\LicenciaController::class, 'resumenPdf']);
    Route::get('licencias/pdf', [App\Http\Controllers\LicenciaController::class, 'listaPdf']);

    Route::get('contribuyente/buscar', [App\Http\Controllers\ContribuyenteController::class, 'buscar']);
    Route::post('contribuyente/actualizar', [App\Http\Controllers\ContribuyenteController::class, 'actualizar']);

    Route::get('permiso', [App\Http\Controllers\PermisoController::class, 'index']);
    Route::post('permiso', [App\Http\Controllers\PermisoController::class, 'store']);
    Route::put('permiso/{permiso}', [App\Http\Controllers\PermisoController::class, 'update']);
    Route::delete('permiso/{permiso}', [App\Http\Controllers\PermisoController::class, 'destroy']);
    Route::get('permiso/roles', [App\Http\Controllers\PermisoController::class, 'roles']);
    Route::post('permiso/asignar', [App\Http\Controllers\PermisoController::class, 'asignar']);

    Route::get('rol', [App\Http\Controllers\RolController::class, 'index']);
    Route::post('rol', [App\Http\Controllers\RolController::class, 'store']);
    Route::put('rol/{rol}', [App\Http\Controllers\RolController::class, 'update']);
    Route::delete('rol/{rol}', [App\Http\Controllers\RolController::class, 'destroy']);

    Route::apiResource('sindicato', App\Http\Controllers\SindicatoController::class);

    Route::apiResource('sancion', App\Http\Controllers\SancionController::class)->except(['show']);

    Route::get('multa/buscar', [App\Http\Controllers\MultaController::class, 'buscarPorPlaca']);
    Route::post('multa', [App\Http\Controllers\MultaController::class, 'store']);
    Route::post('multa/reporte', [App\Http\Controllers\MultaController::class, 'reporte']);
    Route::get('multa/reporte/pdf', [App\Http\Controllers\MultaController::class, 'reportePdf']);
    
});
