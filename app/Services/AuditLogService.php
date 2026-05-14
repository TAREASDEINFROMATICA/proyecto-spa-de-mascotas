<?php

namespace App\Services;

use App\Models\LogSistema;
use Illuminate\Http\Request;

class AuditLogService
{
    /**
     * Registrar una acción en el log
     *
     * @param int|null $userId ID del usuario (null si no está autenticado)
     * @param string $accion Descripción de la acción
     * @param Request|null $request Petición HTTP para obtener IP y User Agent
     * @return void
     */
    public static function registrar($userId, $accion, $request = null)
    {
        $ip = null;
        $userAgent = null;
        
        if ($request) {
            $ip = $request->ip();
            $userAgent = $request->userAgent();
        } else {
            $ip = request()->ip();
            $userAgent = request()->userAgent();
        }
        
        LogSistema::create([
            'id_usuario' => $userId,
            'accion' => $accion,
            'fecha' => now(),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }
}