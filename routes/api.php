<?php

use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\TournamentRegistrationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\League\PlayerController;
use App\Http\Controllers\League\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\ProductController;

// --- RUTAS PÚBLICAS ---
Route::get('/prueba', function () {
    return '¡La ruta de prueba funciona correctamente!';
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/attendance', [AttendanceController::class, 'register']);

// --- RUTAS PROTEGIDAS POR SANCTUM ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Ruta para obtener los datos del usuario logueado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('tournaments', TournamentController::class)->names('admin.tournaments');
    Route::patch('/tournaments/{tournament}/status', [TournamentController::class, 'updateStatus']);
    Route::apiResource('players', PlayerController::class);
    Route::apiResource('teams', TeamController::class);
    Route::post('/teams/{team}', [TeamController::class, 'update']);
   
    // Inscripciones a Torneos
    Route::get('/tournament-registrations', [TournamentRegistrationController::class, 'index']);
    Route::post('/tournament-registrations', [TournamentRegistrationController::class, 'store']);
    Route::patch('/tournament-registrations/{registration}/group', [TournamentRegistrationController::class, 'updateGroup']);
    Route::delete('/tournament-registrations/{registration}', [TournamentRegistrationController::class, 'destroy']);

    // Ruta para registrar e inscribir un jugador directamente a un equipo
    Route::post('/teams/{team}/players', [PlayerController::class, 'storeForTeam']);
    
    // Rutas protegidas además por ROLES (Spatie)
    Route::middleware('role:admin')->group(function () {
        //Route::post('/products', [ProductController::class, 'store']);
        //Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
