<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mascota;
use App\Models\Servicio;
use App\Models\Empleado;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitaController extends Controller
{
    private function getUserFromToken(Request $request)
    {
        $token = $request->query('token') ?? $request->input('token');
        if (!$token) return null;
        $token = trim($token, "'\"");
        $tokenRecord = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if (!$tokenRecord) return null;
        return \App\Models\Usuario::find($tokenRecord->tokenable_id);
    }

    /**
     * Formulario para crear cita (Admin/Recepción)
     */
    public function create(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
            return redirect('/');
        }

        $mascotas = Mascota::where('estado', 'activa')->with('cliente.usuario')->get();
        $servicios = Servicio::where('estado', 'activo')->get();
        $groomers = Empleado::where('cargo', 'Groomer')->with('usuario')->get();

        $token = $request->query('token');

        return view('admin.citas.create', compact('mascotas', 'servicios', 'groomers', 'token'));
    }

    /**
     * Guardar nueva cita (Admin/Recepción)
     */
    public function store(Request $request)
    {
        Log::info('=== INICIO STORE CITA ===');
        Log::info('Datos: ' . json_encode($request->all()));
        
        $user = $this->getUserFromToken($request);
        Log::info('Usuario: ' . ($user ? $user->id_usuario : 'null'));
        
        if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
            Log::info('No autorizado');
            return redirect('/')->with('error', 'No autorizado');
        }
        
        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id_mascota',
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'id_empleado' => 'required|exists:empleados,id_empleado',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
        ]);
        
        // Verificar conflicto
        $conflicto = Cita::where('id_empleado', $request->id_empleado)
            ->where('fecha', $request->fecha)
            ->where('estado', '!=', 'cancelado')
            ->where(function ($query) use ($request) {
                $query->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                      ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('hora_inicio', '<=', $request->hora_inicio)
                            ->where('hora_fin', '>=', $request->hora_fin);
                      });
            })
            ->exists();

        if ($conflicto) {
            return back()->with('error', 'Ya existe una cita en ese horario');
        }
        
        $cita = Cita::create([
            'id_mascota' => $request->id_mascota,
            'id_servicio' => $request->id_servicio,
            'id_empleado' => $request->id_empleado,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'estado' => 'programado',
            'tipo_cita' => 'normal',
            'fecha_registro' => Carbon::now(),
        ]);
        
        Log::info('Cita creada ID: ' . $cita->id_cita);
        
        AuditLogService::registrar(
            $user->id_usuario,
            'Creó cita para mascota ID: ' . $request->id_mascota,
            $request
        );
        
        $token = $request->query('token');
        return redirect('/admin/agenda?fecha=' . $request->fecha . '&token=' . $token)
            ->with('success', '✅ Cita creada correctamente');
    }

    /**
     * Formulario para editar cita (Admin/Recepción)
     */
    public function edit(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
            return redirect('/');
        }

        $cita = Cita::with(['mascota', 'servicio', 'empleado.usuario'])->findOrFail($id);
        
        $mascotas = Mascota::where('estado', 'activa')->with('cliente.usuario')->get();
        $servicios = Servicio::where('estado', 'activo')->get();
        $groomers = Empleado::where('cargo', 'Groomer')->with('usuario')->get();

        $token = $request->query('token');

        return view('admin.citas.edit', compact('cita', 'mascotas', 'servicios', 'groomers', 'token'));
    }

    /**
     * Actualizar cita (Admin/Recepción)
     */
    public function update(Request $request, $id)
    {
        Log::info('=== INICIO UPDATE CITA ===');
        Log::info('Datos: ' . json_encode($request->all()));
        
        $user = $this->getUserFromToken($request);
        
        if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
            return redirect('/')->with('error', 'No autorizado');
        }
        
        $cita = Cita::findOrFail($id);
        
        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id_mascota',
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'id_empleado' => 'required|exists:empleados,id_empleado',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
        ]);
        
        // Verificar conflicto con otras citas (excluyendo esta)
        $conflicto = Cita::where('id_empleado', $request->id_empleado)
            ->where('fecha', $request->fecha)
            ->where('id_cita', '!=', $id)
            ->where('estado', '!=', 'cancelado')
            ->where(function ($query) use ($request) {
                $query->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                      ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('hora_inicio', '<=', $request->hora_inicio)
                            ->where('hora_fin', '>=', $request->hora_fin);
                      });
            })
            ->exists();

        if ($conflicto) {
            return back()->with('error', 'Ya existe otra cita en ese horario');
        }
        
        $cita->update([
            'id_mascota' => $request->id_mascota,
            'id_servicio' => $request->id_servicio,
            'id_empleado' => $request->id_empleado,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
        ]);
        
        Log::info('Cita actualizada ID: ' . $cita->id_cita);
        
        AuditLogService::registrar(
            $user->id_usuario,
            'Editó cita ID: ' . $id,
            $request
        );
        
        $token = $request->query('token');
        return redirect('/admin/agenda?fecha=' . $request->fecha . '&token=' . $token)
            ->with('success', '✅ Cita actualizada correctamente');
    }

    /**
     * Cancelar cita (Admin/Recepción)
     */
    public function cancel(Request $request, $id)
    {
        Log::info('=== CANCELAR CITA ===');
        
        $user = $this->getUserFromToken($request);
        
        if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        $cita = Cita::findOrFail($id);
        
        $motivo = $request->input('motivo');
        $observaciones = $request->input('observaciones');
        
        $textoCancelacion = "CANCELADA - Motivo: $motivo";
        if ($observaciones) {
            $textoCancelacion .= " - Observaciones: $observaciones";
        }
        
        $cita->estado = 'cancelado';
        $cita->observaciones = $textoCancelacion;
        $cita->save();
        
        Log::info('Cita cancelada ID: ' . $cita->id_cita);
        
        AuditLogService::registrar(
            $user->id_usuario,
            'Canceló cita ID: ' . $id . ' - Motivo: ' . $motivo,
            $request
        );
        
        return response()->json(['success' => true, 'message' => 'Cita cancelada correctamente']);
    }

    /**
     * Formulario para cliente solicitar cita
     */
    public function solicitarForm(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user || !$user->esCliente()) {
            return redirect('/')->with('error', 'No autorizado');
        }
        
        $mascotas = Mascota::where('id_cliente', $user->cliente->id_cliente)
            ->where('estado', 'activa')
            ->get();
        $servicios = Servicio::where('estado', 'activo')->get();
        $groomers = Empleado::where('cargo', 'Groomer')->with('usuario')->get();
        
        $token = $request->query('token');
        
        return view('cliente.solicitar-cita', compact('mascotas', 'servicios', 'groomers', 'token'));
    }

   
    /**
     * Cliente ve sus citas
     */
    public function misCitas(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user || !$user->esCliente()) {
            return redirect('/')->with('error', 'No autorizado');
        }
        
        $mascotasIds = Mascota::where('id_cliente', $user->cliente->id_cliente)->pluck('id_mascota');
        
        $citas = Cita::whereIn('id_mascota', $mascotasIds)
            ->with(['mascota', 'servicio', 'empleado.usuario'])
            ->orderBy('fecha', 'desc')
            ->get();
        
        $token = $request->query('token');
        
        return view('cliente.mis-citas', compact('citas', 'token'));
    }

   

    /**
     * Recepción/Admin confirma cita
     */
    public function confirmarCita(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
            return redirect('/')->with('error', 'No autorizado');
        }
        
        $cita = Cita::findOrFail($id);
        $cita->estado = 'programado';
        $cita->save();
        
        AuditLogService::registrar(
            $user->id_usuario,
            'Confirmó cita ID: ' . $id,
            $request
        );
        
        $token = $request->query('token');
        return redirect('/personal/citas-pendientes?token=' . $token)
            ->with('success', '✅ Cita confirmada correctamente');
    }

    /**
     * Agenda maestra (Admin y Recepción)
     */
    public function agendaMaestro(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
            return redirect('/');
        }
        
        $fecha = $request->query('fecha', Carbon::now()->format('Y-m-d'));
        $fechaObj = Carbon::parse($fecha);
        
        $groomers = Empleado::where('cargo', 'Groomer')->with('usuario')->get();
        
        $citas = Cita::where('fecha', $fecha)
            ->where('estado', '!=', 'cancelado')
            ->where('estado', '!=', 'pendiente')
            ->with(['mascota', 'servicio', 'empleado.usuario'])
            ->get()
            ->groupBy('id_empleado');
        
        $token = $request->query('token');
        
        return view('admin.agenda.index', compact('groomers', 'citas', 'fecha', 'fechaObj', 'token'));
    }

    /**
     * Obtener horarios disponibles
     */
    public function getHorariosDisponibles(Request $request)
    {
        $fecha = $request->query('fecha');
        $empleadoId = $request->query('empleado_id');
        $servicioId = $request->query('servicio_id');
        $mascotaId = $request->query('mascota_id');
        $citaId = $request->query('cita_id');

        Log::info("=== HORARIOS DISPONIBLES ===");
        Log::info("fecha: $fecha, empleado: $empleadoId, servicio: $servicioId, mascota: $mascotaId, citaId: $citaId");

        if (!$fecha || !$empleadoId || !$servicioId || !$mascotaId) {
            return response()->json([]);
        }

        $empleado = Empleado::find($empleadoId);
        if (!$empleado) {
            return response()->json([]);
        }

        $turno = $empleado->turno ?? 'Completo';
        $horariosTurno = $this->getHorarioPorTurno($turno);

        $servicio = Servicio::find($servicioId);
        $mascota = Mascota::find($mascotaId);

        if (!$servicio || !$mascota) {
            return response()->json([]);
        }

        $tamaño = $this->getTamañoMascota($mascota);
        $duracion = $this->getDuracionAjustada($servicio->duracion_minutos, $tamaño);

        $slots = [];
        $horaActual = Carbon::parse($horariosTurno['inicio']);
        $horaFin = Carbon::parse($horariosTurno['fin']);

        while ($horaActual->copy()->addMinutes($duracion) <= $horaFin) {
            $slotFin = $horaActual->copy()->addMinutes($duracion);
            
            $conflicto = Cita::where('id_empleado', $empleadoId)
                ->where('fecha', $fecha)
                ->where('estado', '!=', 'cancelado')
                ->when($citaId, function($query) use ($citaId) {
                    return $query->where('id_cita', '!=', $citaId);
                })
                ->where(function ($query) use ($horaActual, $slotFin) {
                    $query->whereBetween('hora_inicio', [$horaActual->format('H:i:s'), $slotFin->format('H:i:s')])
                          ->orWhereBetween('hora_fin', [$horaActual->format('H:i:s'), $slotFin->format('H:i:s')])
                          ->orWhere(function ($q) use ($horaActual, $slotFin) {
                              $q->where('hora_inicio', '<=', $horaActual->format('H:i:s'))
                                ->where('hora_fin', '>=', $slotFin->format('H:i:s'));
                          });
                })
                ->exists();

            if (!$conflicto) {
                $slots[] = [
                    'hora_inicio' => $horaActual->format('H:i'),
                    'hora_fin' => $slotFin->format('H:i'),
                    'duracion' => $duracion
                ];
            }

            $horaActual->addMinutes(15);
        }

        return response()->json($slots);
    }

    private function getHorarioPorTurno($turno)
    {
        $horarios = [
            'Mañana' => ['inicio' => '08:00:00', 'fin' => '14:00:00'],
            'Tarde' => ['inicio' => '14:00:00', 'fin' => '20:00:00'],
            'Noche' => ['inicio' => '20:00:00', 'fin' => '02:00:00'],
            'Completo' => ['inicio' => '08:00:00', 'fin' => '20:00:00'],
        ];

        return $horarios[$turno] ?? $horarios['Completo'];
    }

    private function getTamañoMascota($mascota)
    {
        if ($mascota->peso) {
            if ($mascota->peso <= 10) return 'pequeño';
            if ($mascota->peso <= 25) return 'mediano';
            if ($mascota->peso <= 40) return 'grande';
            return 'gigante';
        }
        
        return 'pequeño';
    }

    private function getDuracionAjustada($duracionBase, $tamaño)
    {
        $tiempoExtra = [
            'pequeño' => 0,
            'mediano' => 15,
            'grande' => 30,
            'gigante' => 45,
        ];
        
        $extra = $tiempoExtra[strtolower($tamaño)] ?? 0;
        $duracion = $duracionBase + $extra;
        
        return ceil($duracion / 15) * 15;
    }
    /**
 * Guardar solicitud de cita (estado: reservado)
 */
public function solicitarStore(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user || !$user->esCliente()) {
        return redirect('/')->with('error', 'No autorizado');
    }
    
    $request->validate([
        'id_mascota' => 'required|exists:mascotas,id_mascota',
        'id_servicio' => 'required|exists:servicios,id_servicio',
        'id_empleado' => 'required|exists:empleados,id_empleado',
        'fecha' => 'required|date',
        'hora_inicio' => 'required',
        'hora_fin' => 'required',
    ]);
    
    $cita = Cita::create([
        'id_mascota' => $request->id_mascota,
        'id_servicio' => $request->id_servicio,
        'id_empleado' => $request->id_empleado,
        'fecha' => $request->fecha,
        'hora_inicio' => $request->hora_inicio,
        'hora_fin' => $request->hora_fin,
        'estado' => 'reservado',  // ← CAMBIADO de 'pendiente' a 'reservado'
        'tipo_cita' => 'normal',
        'fecha_registro' => Carbon::now(),
    ]);
    
    AuditLogService::registrar(
        $user->id_usuario,
        'Solicitó cita para mascota ID: ' . $request->id_mascota,
        $request
    );
    
    $token = $request->query('token');
    return redirect('/cliente/mis-citas?token=' . $token)
        ->with('success', '✅ Cita solicitada. Espera confirmación de recepción.');
}
public function citasPendientes(Request $request)
{
    $user = $this->getUserFromToken($request);
    
    if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
        return redirect('/')->with('error', 'No autorizado');
    }
    $rol = $user->esAdmin() ? 'admin' : 'recepcion';
    
    
    $citas = Cita::where('estado', 'reservado')  // ← CAMBIADO de 'pendiente' a 'reservado'
        ->with(['mascota.cliente.usuario', 'servicio', 'empleado.usuario'])
        ->orderBy('fecha', 'asc')
        ->get();
    
    $token = $request->query('token');
    
    return view('personal.citas-pendientes', compact('citas', 'token', 'rol'));
}
/**
 * Cliente cancela su propia cita
 */
public function clienteCancelar(Request $request, $id)
{
    $user = $this->getUserFromToken($request);
    
    if (!$user || !$user->esCliente()) {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }
    
    $cita = Cita::findOrFail($id);
    
    // Verificar que la cita pertenece al cliente
    $mascota = Mascota::find($cita->id_mascota);
    if (!$mascota || $mascota->id_cliente != $user->cliente->id_cliente) {
        return response()->json(['success' => false, 'message' => 'Esta cita no te pertenece'], 401);
    }
    
    // Solo se puede cancelar si está reservado o programado
    if (!in_array($cita->estado, ['reservado', 'programado'])) {
        return response()->json(['success' => false, 'message' => 'Esta cita ya no se puede cancelar'], 400);
    }
    
    $motivo = $request->input('motivo');
    $observaciones = $request->input('observaciones');
    
    $textoCancelacion = "CANCELADA POR CLIENTE - Motivo: $motivo";
    if ($observaciones) {
        $textoCancelacion .= " - Observaciones: $observaciones";
    }
    
    $cita->estado = 'cancelado';
    $cita->observaciones = $textoCancelacion;
    $cita->save();
    
    AuditLogService::registrar(
        $user->id_usuario,
        'Cliente canceló cita ID: ' . $id . ' - Motivo: ' . $motivo,
        $request
    );
    
    return response()->json(['success' => true, 'message' => 'Cita cancelada correctamente']);
}
}