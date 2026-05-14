<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $userRole = $request->user()->rol->nombre;

        if (!in_array($userRole, $roles)) {
            return response()->json(['message' => 'No tienes permiso para acceder'], 403);
        }

        return $next($request);
    }
}