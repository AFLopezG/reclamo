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
});
