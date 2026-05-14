<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\TwoFAController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

// =========================================================
// RUTAS PÚBLICAS (no requieren autenticación)
// =========================================================
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'message' => 'API funcionando']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/registro', [RegistroController::class, 'register']);
Route::post('/verificar-bloqueo', [AuthController::class, 'verificarBloqueo']);
Route::get('/verificar-email/{token}', [RegistroController::class, 'verifyEmail']);
Route::post('/2fa/verificar-login', [AuthController::class, 'verificar2FALogin']);

// =========================================================
// RUTAS PROTEGIDAS (requieren token)
// =========================================================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/2fa/generar', [TwoFAController::class, 'generar']);
    Route::post('/2fa/verificar', [TwoFAController::class, 'verificar']);
    Route::post('/2fa/desactivar', [TwoFAController::class, 'desactivar']);
    

   
});