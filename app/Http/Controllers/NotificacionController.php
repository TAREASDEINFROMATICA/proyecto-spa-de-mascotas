<?php
namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Cita;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use App\Mail\RecordatorioCitaMail;
use Illuminate\Support\Facades\Mail;

class NotificacionController extends Controller
{
    private function getUserFromToken(Request $request)
    {
        $token = $request->query('token') ?? $request->input('token');
        if (!$token) return null;
        $token = trim($token, "'\"");
        $tokenRecord = PersonalAccessToken::findToken($token);
        if (!$tokenRecord) return null;
        return Usuario::find($tokenRecord->tokenable_id);
    }

    // =========================================================
    // VER MIS NOTIFICACIONES
    // =========================================================
  public function misNotificaciones(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user) {
        return redirect('/');
    }
    
    $notificaciones = Notificacion::where('id_usuario', $user->id_usuario)
        ->orderBy('fecha_envio', 'desc')
        ->paginate(20);
    
    // Marcar como leídas
    Notificacion::where('id_usuario', $user->id_usuario)
        ->where('estado', 'pendiente')
        ->update(['estado' => 'leida']);
    
    $token = $request->query('token');
    
    // =========================================================
    // DETERMINAR ROL CORRECTAMENTE
    // =========================================================
    $rol = '';
    if ($user->esAdmin()) {
        $rol = 'admin';
    } elseif ($user->rol->nombre === 'Groomer') {
        $rol = 'groomer';
    } elseif ($user->rol->nombre === 'Recepcion') {
        $rol = 'recepcion';
    } else {
        $rol = 'cliente';
    }
    
    return view('notificaciones.index', compact('notificaciones', 'token', 'rol'));
}

    // =========================================================
    // CREAR NOTIFICACIÓN (MÉTODO AUXILIAR)
    // =========================================================
    public static function crear($idUsuario, $tipo, $mensaje)
    {
        return Notificacion::create([
            'id_usuario' => $idUsuario,
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'fecha_envio' => Carbon::now(),
            'estado' => 'pendiente'
        ]);
    }

    // =========================================================
    // MARCAR COMO LEÍDA
    // =========================================================
    public function marcarLeida(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$user) {
            return response()->json(['success' => false], 401);
        }
        
        $notificacion = Notificacion::where('id_notificacion', $id)
            ->where('id_usuario', $user->id_usuario)
            ->first();
        
        if ($notificacion) {
            $notificacion->estado = 'leida';
            $notificacion->save();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false]);
    }

    // =========================================================
    // OBTENER CONTADOR DE NOTIFICACIONES NO LEÍDAS
    // =========================================================
    public function contarNoLeidas(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user) {
            return response()->json(['count' => 0]);
        }
        
        $count = Notificacion::where('id_usuario', $user->id_usuario)
            ->where('estado', 'pendiente')
            ->count();
        
        return response()->json(['count' => $count]);
    }

// =========================================================
// ENVIAR RECORDATORIO POR EMAIL
// =========================================================
public static function enviarRecordatorioEmail(Cita $cita, $tipo = 'recordatorio')
{
    try {
        $cliente = $cita->mascota->cliente;
        $email = $cliente->usuario->correo;
        
        Mail::to($email)->send(new RecordatorioCitaMail($cita, $tipo));
        
        // Crear notificación en sistema
        self::crear(
            $cliente->id_usuario,
            $tipo == 'recordatorio' ? 'cita_recordatorio' : 'cita_confirmada',
            $tipo == 'recordatorio' 
                ? "Recordatorio: Tienes una cita para {$cita->mascota->nombre} el {$cita->fecha} a las {$cita->hora_inicio}"
                : "Tu cita para {$cita->mascota->nombre} ha sido confirmada"
        );
        
        return true;
    } catch (\Exception $e) {
        Log::error('Error enviando email: ' . $e->getMessage());
        return false;
    }
}
}