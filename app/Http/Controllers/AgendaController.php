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
     * Obtener duración ajustada según tamaño y temperamento de la mascota
     */
    private function getDuracionAjustada($duracionBase, $tamaño, $temperamento = null)
    {
        // Ajuste por tamaño
        $ajustesTamaño = [
            'pequeño' => 1.0,
            'mediano' => 1.10,
            'grande' => 1.15,
            'gigante' => 1.30,
        ];
        $factor = $ajustesTamaño[strtolower($tamaño)] ?? 1.0;
        $duracion = round($duracionBase * $factor);
        
        // Ajuste por temperamento (nervioso/agresivo = +15 minutos)
        if (in_array(strtolower($temperamento), ['nervioso', 'agresivo'])) {
            $duracion += 15;
        }
        
        return ceil($duracion / 15) * 15;
    }

    /**
     * Obtener tamaño de la mascota basado en peso
     */
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
                return true;
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
        
        if (!$servicio || !$mascota) {
            return response()->json([]);
        }
        
        // Calcular duración ajustada por tamaño y temperamento
        $tamaño = $this->getTamañoMascota($mascota);
        $temperamento = $mascota->temperamento_general;
        $duracion = $this->getDuracionAjustada($servicio->duracion_minutos, $tamaño, $temperamento);
        
        // Validar capacidad diaria del groomer
        $citasHoy = Cita::where('id_empleado', $empleadoId)
            ->where('fecha', $fecha)
            ->whereIn('estado', ['programado', 'reservado'])
            ->count();
        
        $empleado = Empleado::find($empleadoId);
        if ($citasHoy >= $empleado->capacidad_diaria) {
            return response()->json([]);
        }
        
        // Generar slots
        $slots = [];
        $horaActual = Carbon::parse($disponibilidad->hora_inicio);
        $horaFin = Carbon::parse($disponibilidad->hora_fin);
        
        while ($horaActual->copy()->addMinutes($duracion) <= $horaFin) {
            $slotFin = $horaActual->copy()->addMinutes($duracion);
            
            if (!$this->hayConflicto($empleadoId, $fecha, $horaActual->format('H:i:s'), $slotFin->format('H:i:s'))) {
                $slots[] = [
                    'hora_inicio' => $horaActual->format('H:i'),
                    'hora_fin' => $slotFin->format('H:i'),
                    'duracion' => $duracion,
                    'tiempo_extra' => $this->getTiempoExtraInfo($tamaño, $temperamento)
                ];
            }
            
            $horaActual->addMinutes(15);
        }
        
        return response()->json($slots);
    }

    /**
     * Obtener información del tiempo extra para mostrar al cliente
     */
    private function getTiempoExtraInfo($tamaño, $temperamento)
    {
        $info = [];
        if ($tamaño != 'pequeño') {
            $info[] = ucfirst($tamaño);
        }
        if (in_array(strtolower($temperamento), ['nervioso', 'agresivo'])) {
            $info[] = 'Temperamento ' . $temperamento;
        }
        return implode(' + ', $info);
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
        
        $rol = $user->esAdmin() ? 'admin' : 'recepcion';
        $fecha = $request->query('fecha', Carbon::now()->format('Y-m-d'));
        $fechaObj = Carbon::parse($fecha);
        
        $groomers = Empleado::where('cargo', 'Groomer')
            ->with('usuario')
            ->get();
        
        $citas = Cita::where('fecha', $fecha)
            ->with(['mascota', 'servicio', 'empleado.usuario'])
            ->get();
        
        $token = $request->query('token');
        
        return view('admin.agenda.index', compact('groomers', 'citas', 'fecha', 'fechaObj', 'token', 'rol'));
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