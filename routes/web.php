<?php
use Illuminate\Http\Request;
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
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\CitaController;



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




// Módulo de Mascotas
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/cliente/mascotas', MascotaController::class);
    Route::get('/cliente/mascotas/{id}/show', [MascotaController::class, 'show'])->name('mascotas.show');
    Route::resource('/admin/mascotas', MascotaController::class);
});

// Rutas para mascotas (cliente)
Route::prefix('cliente')->middleware('auth:sanctum')->group(function () {
    Route::get('/mascotas', [MascotaController::class, 'index'])->name('cliente.mascotas.index');
    Route::get('/mascotas/create', [MascotaController::class, 'create'])->name('cliente.mascotas.create');
    Route::post('/mascotas', [MascotaController::class, 'store'])->name('cliente.mascotas.store');
    Route::get('/mascotas/{id}/show', [MascotaController::class, 'show'])->name('cliente.mascotas.show');
    Route::get('/mascotas/{id}/edit', [MascotaController::class, 'edit'])->name('cliente.mascotas.edit');
    Route::put('/mascotas/{id}', [MascotaController::class, 'update'])->name('cliente.mascotas.update');
    Route::delete('/mascotas/{id}', [MascotaController::class, 'destroy'])->name('cliente.mascotas.destroy');
});

// Rutas para mascotas (admin)
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/mascotas', [MascotaController::class, 'index'])->name('admin.mascotas.index');
    Route::get('/mascotas/{id}/activate', [MascotaController::class, 'activate'])->name('admin.mascotas.activate');
});
// =========================================================
// MÓDULO DE MASCOTAS (con token por URL)
// =========================================================
// Cliente
Route::get('/cliente/mascotas', function (Request $request) {
    $token = $request->query('token');
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->index($request);
})->name('cliente.mascotas.index');

Route::get('/cliente/mascotas/create', function (Request $request) {
    $token = $request->query('token');
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->create($request);
})->name('cliente.mascotas.create');

Route::post('/cliente/mascotas', function (Request $request) {
    $token = $request->query('token');
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->store($request);
})->name('cliente.mascotas.store');

Route::get('/cliente/mascotas/{id}', function (Request $request, $id) {
    $token = $request->query('token');
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->show($request, $id);
})->name('cliente.mascotas.show');

Route::get('/cliente/mascotas/{id}/edit', function (Request $request, $id) {
    $token = $request->query('token');
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->edit($request, $id);
})->name('cliente.mascotas.edit');

Route::put('/cliente/mascotas/{id}', function (Request $request, $id) {
    $token = $request->query('token') ?? $request->input('token');
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->update($request, $id);
})->name('cliente.mascotas.update');

Route::delete('/cliente/mascotas/{id}', function (Request $request, $id) {
    $token = $request->query('token');
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->destroy($request, $id);
})->name('cliente.mascotas.destroy');

// Admin
Route::get('/admin/mascotas', function (Request $request) {
    $token = $request->query('token');
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->index($request);
})->name('admin.mascotas.index');

Route::get('/admin/mascotas/{id}/activate', function (Request $request, $id) {
    $token = $request->query('token');
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->activate($request, $id);
})->name('admin.mascotas.activate');


//nuevo 
// Módulo de Servicios (solo admin)
Route::get('/admin/servicios', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new ServicioController();
    return $controller->index(request());
})->name('admin.servicios.index');

Route::get('/admin/servicios/create', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new ServicioController();
    return $controller->create(request());
})->name('admin.servicios.create');

Route::post('/admin/servicios', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new ServicioController();
    return $controller->store(request());
})->name('admin.servicios.store');

Route::get('/admin/servicios/{id}/edit', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new ServicioController();
    return $controller->edit(request(), $id);
})->name('admin.servicios.edit');

Route::put('/admin/servicios/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new ServicioController();
    return $controller->update(request(), $id);
})->name('admin.servicios.update');

Route::delete('/admin/servicios/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new ServicioController();
    return $controller->destroy($id);
})->name('admin.servicios.destroy');

Route::get('/admin/servicios/{id}/activate', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new ServicioController();
    return $controller->activate($id);
})->name('admin.servicios.activate');
Route::get('/admin/servicios/{id}/desactivate', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ServicioController();
    return $controller->desactivate($id);
})->name('admin.servicios.desactivate');


// Módulo de Agenda
Route::get('/admin/agenda', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new AgendaController();
    return $controller->agendaMaestro(request());
})->name('admin.agenda.index');

Route::get('/admin/agenda/horarios/{empleadoId}/{fecha}/{servicioId}/{mascotaId}', function ($empleadoId, $fecha, $servicioId, $mascotaId) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new AgendaController();
    return $controller->horariosDisponibles(request(), $empleadoId, $fecha, $servicioId, $mascotaId);
})->name('admin.agenda.horarios');



// Módulo de Citas
Route::get('/admin/citas/create', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new CitaController();
    return $controller->create(request());
})->name('admin.citas.create');

Route::get('/admin/citas/horarios', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new CitaController();
    return $controller->getHorariosDisponibles(request());
})->name('admin.citas.horarios');

Route::post('/admin/citas', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new CitaController();
    return $controller->store(request());
})->name('admin.citas.store');



Route::get('/admin/citas/{id}/edit', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->edit(request(), $id);
})->name('admin.citas.edit');

Route::put('/admin/citas/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->update(request(), $id);
})->name('admin.citas.update');



Route::post('/admin/citas/{id}/cancel', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->cancel(request(), $id);
})->name('admin.citas.cancel');



// Cliente solicita cita
Route::get('/cliente/solicitar-cita', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->solicitarForm(request());
})->name('cliente.cita.solicitar');

Route::post('/cliente/solicitar-cita', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->solicitarStore(request());
})->name('cliente.cita.store');

// Cliente ve sus citas
Route::get('/cliente/mis-citas', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->misCitas(request());
})->name('cliente.citas');

// Recepción ve citas pendientes
Route::get('/personal/citas-pendientes', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->citasPendientes(request());
})->name('personal.citas.pendientes');

// Recepción confirma cita
Route::post('/admin/citas/{id}/confirmar', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->confirmarCita(request(), $id);
})->name('admin.citas.confirmar');



// Recepción
Route::get('/recepcion/dashboard', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    return view('personal.recepcion.dashboard');
})->name('recepcion.dashboard');

// Groomer
Route::get('/groomer/dashboard', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    return view('personal.groomer.dashboard');
})->name('groomer.dashboard');

// Cliente cancela su cita
Route::post('/cliente/citas/{id}/cancelar', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->clienteCancelar(request(), $id);
})->name('cliente.citas.cancelar');


// =========================================================
// RECEPCIÓN - GESTIÓN DE CLIENTES Y MASCOTAS
// =========================================================
// Recepción - Clientes y Mascotas
Route::get('/recepcion/clientes', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteController();
    return $controller->index(request());
})->name('recepcion.clientes');

Route::get('/recepcion/mascotas', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->index(request());
})->name('recepcion.mascotas');

Route::get('/recepcion/clientes/{id}/ver', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteController();
    return $controller->show(request(), $id);
})->name('recepcion.clientes.ver');

Route::get('/personal/citas-pendientes', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->citasPendientes(request());
})->name('personal.citas.pendientes');

// Recepción - Ver detalles de una mascota
Route::get('/recepcion/mascotas/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\MascotaController();
    return $controller->show(request(), $id);
})->name('recepcion.mascotas.show');