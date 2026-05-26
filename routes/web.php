<?php
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Auth\log;
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
use App\Http\Controllers\GroomerController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Request as RequestFacade; 
// Redirigir cuando no hay autenticación
Route::get('/login', function () {
    return redirect('/');
})->name('login');

// =========================================================
// PÁGINAS PÚBLICAS
// =========================================================
// Página de login (raíz)
Route::get('/', function () {
    return view('test-login');
});
Route::get('/registro', [RegistroController::class, 'showForm']);

// =========================================================
// DASHBOARDS POR ROL
// =========================================================
Route::get('/dashboard', function () {
    $token = request()->query('token');
    
    if (!$token) {
        return redirect('/');
    }
    
    request()->headers->set('Authorization', 'Bearer ' . $token);
    
    try {
        $tokenRecord = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if (!$tokenRecord) {
            return redirect('/');
        }
        
        $user = \App\Models\Usuario::find($tokenRecord->tokenable_id);
        if (!$user) {
            return redirect('/');
        }
        
        $rol = $user->rol->nombre;
        
        if ($rol === 'Administrador') {
            return redirect('/admin/dashboard?token=' . $token);
        } elseif ($rol === 'Cliente') {
            return redirect('/cliente/dashboard?token=' . $token);
        } elseif ($rol === 'Groomer') {
            return redirect('/groomer/dashboard?token=' . $token);
        } elseif ($rol === 'Recepcion') {
            return redirect('/recepcion/dashboard?token=' . $token);
        }
        
        return redirect('/');
        
    } catch (\Exception $e) {
       
        return redirect('/');
    }
})->name('dashboard');
Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

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
// Recepción confirma cita (cambia estado de reservado a programado)
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


// Groomer - Dashboard principal
Route::get('/groomer/dashboard', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    return view('personal.groomer.dashboard');
})->name('groomer.dashboard');

// Groomer - Mis Citas
Route::get('/groomer/mis-citas', function () {
    $token = request()->query('token');
    if (!$token) {
        return redirect('/');
    }
    request()->headers->set('Authorization', 'Bearer ' . $token);
    $controller = new GroomerController();
    return $controller->misCitas(request());
})->name('groomer.mis-citas');

// Groomer - Mascotas Asignadas
Route::get('/groomer/mis-mascotas', function () {
    $token = request()->query('token');
    if (!$token) {
        return redirect('/');
    }
    request()->headers->set('Authorization', 'Bearer ' . $token);
    $controller = new GroomerController();
    return $controller->misMascotas(request());
})->name('groomer.mis-mascotas');

// Groomer - Checklist
Route::get('/groomer/checklist', function () {
    $token = request()->query('token');
    if (!$token) {
        return redirect('/');
    }
    request()->headers->set('Authorization', 'Bearer ' . $token);
    $controller = new GroomerController();
    return $controller->checklist(request());
})->name('groomer.checklist');

// Groomer - Galería
Route::get('/groomer/galeria', function () {
    $token = request()->query('token');
    if (!$token) {
        return redirect('/');
    }
    request()->headers->set('Authorization', 'Bearer ' . $token);
    $controller = new GroomerController();
    return $controller->galeria(request());
})->name('groomer.galeria');

// Groomer - Insumos
Route::get('/groomer/insumos', function () {
    $token = request()->query('token');
    if (!$token) {
        return redirect('/');
    }
    request()->headers->set('Authorization', 'Bearer ' . $token);
    $controller = new GroomerController();
    return $controller->insumos(request());
})->name('groomer.insumos');

Route::get('/groomer/ficha-tecnica/{citaId}', function ($citaId) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->fichaTecnica(request(), $citaId);
})->name('groomer.ficha-tecnica');

// Groomer - Cerrar servicio
Route::post('/groomer/cerrar-servicio/{citaId}', function ($citaId) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->cerrarServicio(request(), $citaId);
})->name('groomer.cerrar-servicio');

// Groomer - Subir foto
Route::post('/groomer/subir-foto-directo', function (Request $request) {
    $token = $request->query('token');
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->subirFotoDirecto($request);
})->name('groomer.subir.foto.directo');
// Groomer - ver ficha 
Route::get('/groomer/ficha-tecnica-ver/{citaId}', function ($citaId) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->fichaTecnicaVer(request(), $citaId);
})->name('groomer.ficha-tecnica-ver');

// Groomer - Guardar progreso
Route::post('/groomer/guardar-progreso/{citaId}', function ($citaId) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->guardarProgreso(request(), $citaId);
})->name('groomer.guardar-progreso');


// Groomer - Insumos disponibles
Route::get('/groomer/insumos-disponibles', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->getInsumosDisponibles(request());
})->name('groomer.insumos.disponibles');

// Groomer - Registrar consumo de insumos
Route::post('/groomer/consumo-insumos/{citaId}', function ($citaId) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->registrarConsumoInsumo(request(), $citaId);
})->name('groomer.consumo.insumos');

// Groomer - Ver consumos de una cita
Route::get('/groomer/consumos-cita/{citaId}', function ($citaId) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->getConsumosByCita(request(), $citaId);
})->name('groomer.consumos.cita');

// Groomer - Mis consumos (historial)
Route::get('/groomer/mis-consumos', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->misConsumos(request());
})->name('groomer.mis.consumos');


// =========================================================
// ADMIN - GESTIÓN DE INSUMOS
// =========================================================
Route::get('/admin/insumos', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\InsumoController();
    return $controller->index(request());
})->name('admin.insumos.index');

Route::get('/admin/insumos/create', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\InsumoController();
    return $controller->create(request());
})->name('admin.insumos.create');

Route::post('/admin/insumos', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\InsumoController();
    return $controller->store(request());
})->name('admin.insumos.store');

Route::get('/admin/insumos/{id}/edit', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\InsumoController();
    return $controller->edit(request(), $id);
})->name('admin.insumos.edit');

Route::put('/admin/insumos/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\InsumoController();
    return $controller->update(request(), $id);
})->name('admin.insumos.update');

Route::post('/admin/insumos/{id}/ajustar-stock', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\InsumoController();
    return $controller->ajustarStock(request(), $id);
})->name('admin.insumos.ajustar');

Route::get('/admin/insumos/{id}/toggle', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\InsumoController();
    return $controller->toggleEstado(request(), $id);
})->name('admin.insumos.toggle');

// =========================================================
// ADMIN - GESTIÓN DE PRODUCTOS (TIENDA)
// =========================================================
Route::get('/admin/productos', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ProductoVentaController();
    return $controller->index(request());
})->name('admin.productos.index');

Route::get('/admin/productos/create', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ProductoVentaController();
    return $controller->create(request());
})->name('admin.productos.create');

Route::post('/admin/productos', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ProductoVentaController();
    return $controller->store(request());
})->name('admin.productos.store');

Route::get('/admin/productos/{id}/edit', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ProductoVentaController();
    return $controller->edit(request(), $id);
})->name('admin.productos.edit');

Route::put('/admin/productos/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ProductoVentaController();
    return $controller->update(request(), $id);
})->name('admin.productos.update');

Route::get('/admin/productos/{id}/toggle', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ProductoVentaController();
    return $controller->toggleEstado(request(), $id);
})->name('admin.productos.toggle');

// =========================================================
// GROOMER - CONSUMO DE INSUMOS
// =========================================================
Route::post('/groomer/consumo-insumos/{citaId}', function ($citaId) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\GroomerController();
    return $controller->registrarConsumoInsumo(request(), $citaId);
})->name('groomer.consumo.insumos');

// =========================================================
// GROOMER - INSUMOS DISPONIBLES (para el select)
// =========================================================
Route::get('/groomer/insumos-disponibles', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\GroomerController();
    return $controller->getInsumosDisponibles(request());
})->name('groomer.insumos.disponibles');

// =========================================================
// GROOMER - MIS CONSUMOS (historial)
// =========================================================
Route::get('/groomer/mis-consumos', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\GroomerController();
    return $controller->misConsumos(request());
})->name('groomer.mis.consumos');

// =========================================================
// GROOMER - CONSUMOS POR CITA
// =========================================================
Route::get('/groomer/consumos-cita/{citaId}', function ($citaId) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\GroomerController();
    return $controller->getConsumosByCita(request(), $citaId);
})->name('groomer.consumos.cita');

// =========================================================
// ADMIN - REPORTES Y VENTAS
// =========================================================
Route::get('/admin/reportes', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ReporteController();
    return $controller->financieros(request());
})->name('admin.reportes');



Route::get('/admin/alertas-stock', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ReporteController();
    return $controller->alertasStock(request());
})->name('admin.alertas-stock');

Route::get('/admin/citas', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    return view('admin.citas', ['token' => $token]);
})->name('admin.citas');


// =========================================================
// ADMIN - GESTIÓN DE CATEGORÍAS DE PRODUCTOS
// =========================================================
Route::get('/admin/categorias', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CategoriaProductoController();
    return $controller->index(request());
})->name('admin.categorias.index');

Route::get('/admin/categorias/create', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CategoriaProductoController();
    return $controller->create(request());
})->name('admin.categorias.create');

Route::post('/admin/categorias', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CategoriaProductoController();
    return $controller->store(request());
})->name('admin.categorias.store');

Route::get('/admin/categorias/{id}/edit', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CategoriaProductoController();
    return $controller->edit(request(), $id);
})->name('admin.categorias.edit');

Route::put('/admin/categorias/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CategoriaProductoController();
    return $controller->update(request(), $id);
})->name('admin.categorias.update');

Route::delete('/admin/categorias/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CategoriaProductoController();
    return $controller->destroy(request(), $id);
})->name('admin.categorias.destroy');

// Para INSUMOS - Guardar imagen después
Route::post('/admin/insumos/{id}/imagen', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\InsumoController();
    return $controller->guardarImagen(request(), $id);
})->name('admin.insumos.imagen');

// Para PRODUCTOS - Guardar imagen después
Route::post('/admin/productos/{id}/imagen', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ProductoVentaController();
    return $controller->guardarImagen(request(), $id);
})->name('admin.productos.imagen');




// Groomer - Mis calificaciones
Route::get('/groomer/mis-calificaciones', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->misCalificaciones(request());
})->name('groomer.mis-calificaciones');

// Groomer - Exportar CSV
Route::get('/groomer/exportar-csv', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->exportarServiciosCSV(request());
})->name('groomer.exportar.csv');

// Groomer - Mis estadísticas
Route::get('/groomer/mis-estadisticas', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new GroomerController();
    return $controller->misEstadisticas(request());
})->name('groomer.mis-estadisticas');

Route::post('/cliente/calificar/{citaId}', function ($citaId) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CalificacionController();
    return $controller->store(request(), $citaId);
})->name('cliente.calificar');


// =========================================================
// VENTAS
// =========================================================
Route::get('/admin/ventas', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\VentaController();
    return $controller->index(request());
})->name('admin.ventas.index');

Route::get('/admin/ventas/create', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\VentaController();
    return $controller->create(request());
})->name('admin.ventas.create');

Route::post('/admin/ventas', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\VentaController();
    return $controller->store(request());
})->name('admin.ventas.store');

Route::get('/admin/ventas/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\VentaController();
    return $controller->show(request(), $id);
})->name('admin.ventas.show');

// =========================================================
// API - PRODUCTOS PARA VENTAS (búsqueda)
// =========================================================
Route::get('/api/productos/buscar', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\VentaController();
    return $controller->buscarProductos(request());
})->name('api.productos.buscar');

// =========================================================
// CLIENTE - TIENDA (CARRITO Y COMPRAS)
// =========================================================
Route::get('/cliente/tienda', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteTiendaController();
    return $controller->catalogo(request());
})->name('cliente.catalogo');

Route::get('/cliente/carrito', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteTiendaController();
    return $controller->verCarrito(request());
})->name('cliente.carrito');

Route::post('/cliente/carrito/agregar', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteTiendaController();
    return $controller->agregarCarrito(request());
})->name('cliente.carrito.agregar');

Route::post('/cliente/carrito/actualizar', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteTiendaController();
    return $controller->actualizarCarrito(request());
})->name('cliente.carrito.actualizar');

Route::get('/cliente/carrito/eliminar/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteTiendaController();
    return $controller->eliminarCarrito(request(), $id);
})->name('cliente.carrito.eliminar');

Route::get('/cliente/checkout', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteTiendaController();
    return $controller->checkout(request());
})->name('cliente.checkout');

Route::post('/cliente/procesar-pago', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteTiendaController();
    return $controller->procesarPago(request());
})->name('cliente.procesar.pago');

Route::get('/cliente/mis-compras', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteTiendaController();
    return $controller->misCompras(request());
})->name('cliente.mis-compras');

Route::get('/cliente/mis-compras/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ClienteTiendaController();
    return $controller->detalleCompra(request(), $id);
})->name('cliente.detalle-compra');

// =========================================================
// ADMIN - VER TODAS LAS CITAS
// =========================================================
Route::get('/admin/citas/todas', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\CitaController();
    return $controller->todasCitas(request());
})->name('admin.citas.todas');


// =========================================================
// ADMIN - REPORTES
// =========================================================
Route::get('/admin/reportes-financieros', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ReporteController();
    return $controller->financieros(request());
})->name('admin.reportes.financieros');

Route::get('/admin/alertas-stock', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\ReporteController();
    return $controller->alertasStock(request());
})->name('admin.alertas.stock');



// =========================================================
// NOTIFICACIONES
// =========================================================
Route::get('/mis-notificaciones', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\NotificacionController();
    return $controller->misNotificaciones(request());
})->name('notificaciones.index');

Route::post('/notificaciones/{id}/leer', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\NotificacionController();
    return $controller->marcarLeida(request(), $id);
})->name('notificaciones.leer');

Route::get('/notificaciones/count', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\NotificacionController();
    return $controller->contarNoLeidas(request());
})->name('notificaciones.count');



// =========================================================
// ADMIN - DÍAS NO LABORABLES (FERIADOS, MANTENIMIENTO, ETC)
// =========================================================
Route::get('/admin/dias-no-laborables', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\DiaNoLaborableController();
    return $controller->index(request());
})->name('admin.dias-no-laborables.index');

Route::get('/admin/dias-no-laborables/create', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\DiaNoLaborableController();
    return $controller->create(request());
})->name('admin.dias-no-laborables.create');

Route::post('/admin/dias-no-laborables', function () {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\DiaNoLaborableController();
    return $controller->store(request());
})->name('admin.dias-no-laborables.store');

Route::get('/admin/dias-no-laborables/{id}/edit', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\DiaNoLaborableController();
    return $controller->edit(request(), $id);
})->name('admin.dias-no-laborables.edit');

Route::put('/admin/dias-no-laborables/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\DiaNoLaborableController();
    return $controller->update(request(), $id);
})->name('admin.dias-no-laborables.update');

Route::delete('/admin/dias-no-laborables/{id}', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\DiaNoLaborableController();
    return $controller->destroy(request(), $id);
})->name('admin.dias-no-laborables.destroy');

Route::post('/admin/dias-no-laborables/{id}/toggle', function ($id) {
    $token = request()->query('token');
    if ($token) {
        request()->headers->set('Authorization', 'Bearer ' . $token);
    }
    $controller = new \App\Http\Controllers\DiaNoLaborableController();
    return $controller->toggleEstado(request(), $id);
})->name('admin.dias-no-laborables.toggle');


// =========================================================
// GROOMER - CHECKLIST
// =========================================================
Route::get('/groomer/checklist', [App\Http\Controllers\ChecklistController::class, 'index'])->name('groomer.checklist');
Route::get('/groomer/checklist/{citaId}/items', [App\Http\Controllers\ChecklistController::class, 'getChecklist'])->name('groomer.checklist.items');
Route::post('/groomer/checklist/{citaId}/guardar', [App\Http\Controllers\ChecklistController::class, 'guardarProgreso'])->name('groomer.checklist.guardar');
Route::post('/groomer/checklist/{citaId}/completar', [App\Http\Controllers\ChecklistController::class, 'completarChecklist'])->name('groomer.checklist.completar');