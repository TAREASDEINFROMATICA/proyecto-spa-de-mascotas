<?php
namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Cita;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class CalificacionController extends Controller
{
    private function getUserFromToken(Request $request)
    {
        $token = $request->query('token') ?? $request->input('token');
        if (!$token) return null;
        $token = trim($token, "'\"");
        $tokenRecord = PersonalAccessToken::findToken($token);
        if (!$tokenRecord) return null;
        return \App\Models\Usuario::find($tokenRecord->tokenable_id);
    }

    public function store(Request $request, $citaId)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Cliente') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        $cita = Cita::with('mascota')->find($citaId);
        if (!$cita) {
            return response()->json(['success' => false, 'message' => 'Cita no encontrada'], 404);
        }
        
        // Verificar que la cita pertenece al cliente
        if ($cita->mascota->id_cliente != $user->cliente->id_cliente) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        // Verificar que la cita está concluida
        if ($cita->estado !== 'concluido') {
            return response()->json(['success' => false, 'message' => 'El servicio aún no ha finalizado'], 400);
        }
        
        // Verificar que no esté ya calificada
        $existe = Calificacion::where('id_cita', $citaId)->exists();
        if ($existe) {
            return response()->json(['success' => false, 'message' => 'Ya calificaste este servicio'], 400);
        }
        
        $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500'
        ]);
        
        Calificacion::create([
            'id_cita' => $citaId,
            'puntuacion' => $request->puntuacion,
            'comentario' => $request->comentario,
            'fecha_calificacion' => now()
        ]);
        
        return response()->json(['success' => true, 'message' => '¡Gracias por tu calificación!']);
    }
}