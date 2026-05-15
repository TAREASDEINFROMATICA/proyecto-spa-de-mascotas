<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Servicio;
use App\Models\Mascota;
use App\Models\Cita;
use App\Models\DisponibilidadEmpleado;
use App\Models\BloqueoAgenda;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgendaController extends Controller
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
     * Obtener duración ajustada según el tamaño de la mascota
     */
    private function getDuracionAjustada($duracionBase, $tamaño)
    {
        $ajustes = [
            'pequeño' => 1.0,
            'mediano' => 1.10,
            'grande' => 1.15,
            'gigante' => 1.30,
        ];
        $factor = $ajustes[strtolower($tamaño)] ?? 1.0;
        return round($duracionBase * $factor);
    }

    /**
     * Verificar si hay conflictos de horario
     */
    private function hayConflicto($empleadoId, $fecha, $horaInicio, $horaFin, $citaId = null)
    {
        $query = Cita::where('id_empleado', $empleadoId)
            ->where('fecha', $fecha)
            ->where('estado', '!=', 'cancelado');
        
        if ($citaId) {
            $query->where('id_cita', '!=', $citaId);
        }
        
        $citas = $query->get();
        
        foreach ($citas as $cita) {
            if ($horaInicio < $cita->hora_fin && $horaFin > $cita->hora_inicio) {
                return true; // Hay conflicto
            }
        }
        return false;
    }

    /**
     * Obtener horarios disponibles para un groomer
     */
    public function horariosDisponibles(Request $request, $empleadoId, $fecha, $servicioId, $mascotaId)
    {
        $fechaObj = Carbon::parse($fecha);
        $diaSemana = $this->getDiaSemana($fechaObj->dayOfWeek);
        
        // Obtener disponibilidad del empleado
        $disponibilidad = DisponibilidadEmpleado::where('id_empleado', $empleadoId)
            ->where('dia_semana', $diaSemana)
            ->where('estado', 'disponible')
            ->first();
        
        if (!$disponibilidad) {
            return response()->json([]);
        }
        
        // Obtener servicio y mascota
        $servicio = Servicio::find($servicioId);
        $mascota = Mascota::find($mascotaId);
        
        if (!$servicio) {
            return response()->json([]);
        }
        
        // Calcular duración ajustada por tamaño
        $tamaño = $mascota->tamaño ?? 'pequeño';
        $duracion = $this->getDuracionAjustada($servicio->duracion_minutos, $tamaño);
        
        // Generar slots
        $slots = [];
        $horaActual = Carbon::parse($disponibilidad->hora_inicio);
        $horaFin = Carbon::parse($disponibilidad->hora_fin);
        
        while ($horaActual->copy()->addMinutes($duracion) <= $horaFin) {
            $slotFin = $horaActual->copy()->addMinutes($duracion);
            
            // Verificar conflictos
            if (!$this->hayConflicto($empleadoId, $fecha, $horaActual->format('H:i:s'), $slotFin->format('H:i:s'))) {
                $slots[] = [
                    'hora_inicio' => $horaActual->format('H:i'),
                    'hora_fin' => $slotFin->format('H:i'),
                    'duracion' => $duracion
                ];
            }
            
            $horaActual->addMinutes(15); // Avanzar en intervalos de 15 minutos
        }
        
        return response()->json($slots);
    }

    /**
     * Vista de agenda para admin/recepción
     */
   public function agendaMaestro(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
        return redirect('/');
    }
    
    // Determinar el rol para la redirección
    $rol = $user->esAdmin() ? 'admin' : 'recepcion';
    
    $fecha = $request->query('fecha', Carbon::now()->format('Y-m-d'));
    $fechaObj = Carbon::parse($fecha);
    
    // Obtener groomers
    $groomers = Empleado::where('cargo', 'Groomer')
        ->with('usuario')
        ->get();
    
    // Obtener citas del día
    $citas = Cita::where('fecha', $fecha)
        ->with(['mascota.cliente.usuario', 'servicio', 'empleado.usuario'])
        ->get();
    
    $token = $request->query('token');
    
    // Pasar $rol a la vista
    return view('admin.agenda.index', compact('groomers', 'citas', 'fecha', 'fechaObj', 'token', 'rol'));
}

    /**
     * Formulario para crear cita (admin/recepción)
     */
    public function createCita(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
            return redirect('/');
        }
        
        $groomers = Empleado::where('cargo', 'Groomer')->with('usuario')->get();
        $servicios = Servicio::where('estado', 'activo')->get();
        $mascotas = Mascota::where('estado', 'activa')->with('cliente.usuario')->get();
        
        $token = $request->query('token');
        
        return view('admin.agenda.create', compact('groomers', 'servicios', 'mascotas', 'token'));
    }

    private function getDiaSemana($dayOfWeek)
    {
        $dias = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            0 => 'Domingo',
        ];
        return $dias[$dayOfWeek] ?? 'Lunes';
    }
}