<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
//use App\Http\Controllers\ProductController;

// --- RUTAS PÚBLICAS ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// --- RUTAS PROTEGIDAS POR SANCTUM ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Ruta para obtener los datos del usuario logueado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rutas protegidas además por ROLES (Spatie)
    Route::middleware('role:admin')->group(function () {
        //Route::post('/products', [ProductController::class, 'store']);
        //Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
