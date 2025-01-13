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


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('me', [App\Http\Controllers\UserController::class, 'me']);
    Route::post('logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::post('cambioEstado/{id}', [App\Http\Controllers\UserController::class, 'cambioEstado']);
    Route::apiResource('user', App\Http\Controllers\UserController::class);

    Route::post('reportFecha',[\App\Http\Controllers\FormularioController::class,'reportFecha']); 
});
