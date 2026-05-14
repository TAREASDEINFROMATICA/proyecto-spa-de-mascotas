<?php

use App\Models\Usuario;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\TwoFAController;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Controllers\LogController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PerfilClienteController;
use Illuminate\Support\Facades\Mail;

// Redirigir cuando no hay autenticación
Route::get('/login', function () {
    return redirect('/');
})->name('login');

// =========================================================
// PÁGINAS PÚBLICAS
// =========================================================
Route::get('/', function () {
    return view('test-login');
});

Route::get('/registro', [RegistroController::class, 'showForm']);

// =========================================================
// DASHBOARDS POR ROL
// =========================================================
Route::get('/dashboard', function () {
    return view('test-login');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/personal/dashboard', function () {
    return view('personal.dashboard');
})->name('personal.dashboard');

Route::get('/cliente/dashboard', function () {
    return view('cliente.dashboard');
})->name('cliente.dashboard');

// =========================================================
// CAPTCHA
// =========================================================
Route::get('/captcha/generate', [CaptchaController::class, 'generate']);
Route::post('/captcha/verify', [CaptchaController::class, 'verify']);

// =========================================================
// API RUTAS (LOGIN, REGISTRO)
// =========================================================
Route::post('/api/login', [AuthController::class, 'login']);
Route::post('/api/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/api/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/api/registro', [RegistroController::class, 'register']);
Route::get('/verificar-email/{token}', [RegistroController::class, 'verifyEmail']);
Route::post('/api/2fa/verificar-login', [AuthController::class, 'verificar2FALogin']);
Route::post('/reenviar-verificacion', [RegistroController::class, 'reenviarVerificacion']);

// =========================================================
// CONFIGURACIÓN 2FA
// =========================================================
Route::get('/admin/configurar-2fa', function () {
    $token = request()->query('token');
    
    if (!$token) {
        return "<h3>⚠️ No estás autenticado</h3><p><a href='/'>Ir al login</a></p>";
    }
    
    $tokenRecord = PersonalAccessToken::findToken($token);
    
    if (!$tokenRecord) {
        return "<h3>⚠️ Token inválido o expirado</h3><p><a href='/'>Ir al login</a></p>";
    }
    
    $user = Usuario::find($tokenRecord->tokenable_id);
    
    if (!$user || $user->rol->nombre !== 'Administrador') {
        return "<h3>⚠️ No tienes permisos de administrador</h3><p><a href='/'>Ir al login</a></p>";
    }
    
    return view('admin.configurar-2fa', ['token' => $token]);
});

// API RUTAS PARA 2FA
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/api/2fa/generar', [TwoFAController::class, 'generar']);
    Route::post('/api/2fa/verificar', [TwoFAController::class, 'verificar']);
    Route::post('/api/2fa/desactivar', [TwoFAController::class, 'desactivar']);
});

Route::get('/admin/ir-2fa', function () {
    return view('admin.ir-2fa');
});

// =========================================================
// RUTAS DE EMPLEADOS
// =========================================================
Route::get('/admin/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
Route::get('/admin/empleados/create', [EmpleadoController::class, 'create'])->name('empleados.create');
Route::post('/admin/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
Route::get('/admin/empleados/{id}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit');
Route::put('/admin/empleados/{id}', [EmpleadoController::class, 'update'])->name('empleados.update');
Route::delete('/admin/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
Route::put('/admin/empleados/{id}/activate', [EmpleadoController::class, 'activate'])->name('empleados.activate');

// =========================================================
// LOGS DE AUDITORÍA
// =========================================================
Route::get('/admin/logs', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new App\Http\Controllers\LogController();
    return $controller->index(request());
})->name('logs.index');

// =========================================================
// GOOGLE OAUTH
// =========================================================
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('/google/callback', function () {
    return view('google-callback');
});

// =========================================================
// RUTAS DE CLIENTES (ADMIN)
// =========================================================
Route::get('/admin/clientes', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new ClienteController();
    return $controller->index(request());
})->name('clientes.index');

Route::get('/admin/clientes/{id}/desactivar', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new ClienteController();
    return $controller->desactivar($id);
})->name('clientes.desactivar');

Route::get('/admin/clientes/{id}/activar', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new ClienteController();
    return $controller->activar($id);
})->name('clientes.activar');

// =========================================================
// PERFIL DE CLIENTE (SIN auth()->login)
// =========================================================
Route::get('/cliente/perfil', function () {
    $token = request()->query('token');
    
    if (!$token) {
        return redirect('/');
    }
    
    $tokenRecord = PersonalAccessToken::findToken($token);
    if (!$tokenRecord) {
        return redirect('/');
    }
    
    $user = Usuario::find($tokenRecord->tokenable_id);
    if (!$user || $user->rol->nombre !== 'Cliente') {
        return redirect('/');
    }
    
    // Pasar el usuario y token a la vista
    $cliente = $user->cliente;
    return view('cliente.perfil', compact('user', 'cliente', 'token'));
})->name('cliente.perfil');

Route::put('/cliente/perfil', function () {
    $token = request()->query('token') ?? request()->input('token');
    
    if (!$token) {
        return redirect('/');
    }
    
    $tokenRecord = PersonalAccessToken::findToken($token);
    if (!$tokenRecord) {
        return redirect('/');
    }
    
    $user = Usuario::find($tokenRecord->tokenable_id);
    if (!$user || $user->rol->nombre !== 'Cliente') {
        return redirect('/');
    }
    
    // Crear una instancia del controlador y pasar el usuario manualmente
    $controller = new PerfilClienteController();
    return $controller->update(request(), $user);
})->name('cliente.perfil.update');

// Cambiar contraseña (para empleados y clientes)
Route::post('/cambiar-contrasena', [AuthController::class, 'cambiarContrasena'])->middleware('auth:sanctum');