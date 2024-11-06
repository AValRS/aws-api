<?php
// Rutas para API

use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::group(['prefix' => 'alumnos'], function () {
        Route::post('/', [StudentController::class, 'store']);
        Route::put('/{id}', [StudentController::class, 'update']);
        Route::delete('/{id}', [StudentController::class, 'delete']);
        Route::get('/', [StudentController::class, 'find']);
        Route::get('/{id}', [StudentController::class, 'get']);
    });
    Route::group(['prefix' => 'profesores'], function () {
        Route::post('/', [ProfessorController::class, 'store']);
        Route::put('/{id}', [ProfessorController::class, 'update']);
        Route::delete('/{id}', [ProfessorController::class, 'delete']);
        Route::get('/', [ProfessorController::class, 'find']);
        Route::get('/{id}', [ProfessorController::class, 'get']);
    });

});
