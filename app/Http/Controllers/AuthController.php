<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Services\AuditLogService;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Verificar bloqueo
        if ($usuario->estado === 'bloqueado' || ($usuario->blocked_until && Carbon::now()->lt($usuario->blocked_until))) {
            $blockedUntil = $usuario->blocked_until;
            $minutosRestantes = Carbon::now()->diffInMinutes($blockedUntil);
            $minutosRestantes = max(0, $minutosRestantes);
            
            return response()->json([
                'success' => false,
                'blocked' => true,
                'message' => '🔒 CUENTA BLOQUEADA',
                'details' => "Tu cuenta ha sido bloqueada por 15 minutos debido a múltiples intentos fallidos.",
                'tiempo_restante' => "Intenta nuevamente en {$minutosRestantes} minutos.",
                'minutos' => $minutosRestantes
            ], 423);
        }

        // Verificar contraseña
        if (!Hash::check($request->contrasena, $usuario->contrasena_hash)) {
            $usuario->login_attempts = $usuario->login_attempts + 1;
            AuditLogService::registrar(null, 'Intento de inicio de sesión fallido - Email: ' . $request->correo, $request);
            
            if ($usuario->login_attempts >= 5) {
                $usuario->estado = 'bloqueado';
                $usuario->blocked_until = Carbon::now()->addMinutes(15);
                $usuario->save();
                
                return response()->json([
                    'success' => false,
                    'blocked' => true,
                    'message' => '🔒 CUENTA BLOQUEADA',
                    'details' => "Has superado los 5 intentos permitidos. Tu cuenta ha sido bloqueada por 15 minutos.",
                    'tiempo_restante' => "Intenta nuevamente en 15 minutos.",
                    'minutos' => 15
                ], 423);
            }
            
            $intentosRestantes = 5 - $usuario->login_attempts;
            $usuario->save();
            
            return response()->json([
                'success' => false,
                'message' => "❌ Credenciales incorrectas. Te quedan {$intentosRestantes} intentos antes de que tu cuenta se bloquee."
            ], 401);
        }

        // Verificar estado activo
        if ($usuario->estado !== 'activo') {
            return response()->json([
                'success' => false,
                'message' => '❌ Tu cuenta está ' . $usuario->estado . '. Contacta al administrador.'
            ], 401);
        }

        // Login exitoso
        $usuario->login_attempts = 0;
        $usuario->blocked_until = null;
        $usuario->estado = 'activo';
        $usuario->save();

        $usuario->load('rol');
        
        // Verificar 2FA para administrador
        if ($usuario->esAdmin() && $usuario->two_factor_secret) {
            session(['2fa_user_id' => $usuario->id_usuario]);
            
            return response()->json([
                'success' => false,
                'requires_2fa' => true,
                'user_id' => $usuario->id_usuario,
                'message' => 'Ingresa tu código de autenticación de dos factores'
            ], 401);
        }
        
        $token = $usuario->createToken('auth_token')->plainTextToken;

        // =========================================================
        // REDIRECCIONES CON TOKEN EN LA URL
        // =========================================================
        $redirectUrl = '';
        if ($usuario->rol->nombre === 'Administrador') {
            $redirectUrl = '/admin/dashboard?token=' . $token;
        } elseif ($usuario->rol->nombre === 'Recepcion') {
            $redirectUrl = '/recepcion/dashboard?token=' . $token;
        } elseif ($usuario->rol->nombre === 'Groomer') {
            $redirectUrl = '/groomer/dashboard?token=' . $token;
        } else {
            $redirectUrl = '/cliente/dashboard?token=' . $token;
        }

        AuditLogService::registrar($usuario->id_usuario, 'Inicio de sesión exitoso', $request);
        
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'redirect' => $redirectUrl,
            'user' => [
                'id' => $usuario->id_usuario,
                'nombre_completo' => $usuario->nombres . ' ' . $usuario->apellidos,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol->nombre
            ]
        ]);
    }

    public function verificar2FALogin(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|size:6'
        ]);
        
        $userId = session('2fa_user_id');
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Sesión expirada, inicia sesión nuevamente'], 400);
        }
        
        $usuario = Usuario::find($userId);
        
        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }
        
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        
        if ($google2fa->verifyKey($usuario->two_factor_secret, $request->codigo)) {
            session()->forget('2fa_user_id');
            
            $token = $usuario->createToken('auth_token')->plainTextToken;
            
            $usuario->load('rol');
            
            // Redirección con token
            $redirectUrl = '/admin/dashboard?token=' . $token;
            
            return response()->json([
                'success' => true,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'redirect' => $redirectUrl,
                'user' => [
                    'id' => $usuario->id_usuario,
                    'nombre_completo' => $usuario->nombres . ' ' . $usuario->apellidos,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol->nombre
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Código 2FA incorrecto'], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user) {
            AuditLogService::registrar($user->id_usuario, 'Cierre de sesión', $request);
            $user->currentAccessToken()->delete();
        }
        
        return response()->json(['success' => true, 'message' => 'Sesión cerrada']);
    }
    
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('rol');
        return response()->json(['user' => $user]);
    }
    
    public function verificarBloqueo(Request $request)
    {
        $request->validate([
            'correo' => 'required|email'
        ]);
        
        $usuario = Usuario::where('correo', $request->correo)->first();
        
        if (!$usuario) {
            return response()->json(['blocked' => false]);
        }
        
        if ($usuario->blocked_until && Carbon::now()->lt($usuario->blocked_until)) {
            $minutosRestantes = Carbon::now()->diffInMinutes($usuario->blocked_until);
            $segundosRestantes = Carbon::now()->diffInSeconds($usuario->blocked_until);
            
            return response()->json([
                'blocked' => true,
                'message' => '🔒 CUENTA BLOQUEADA',
                'details' => 'Tu cuenta ha sido bloqueada por 15 minutos debido a múltiples intentos fallidos.',
                'tiempo_restante' => "Intenta nuevamente en {$minutosRestantes} minutos.",
                'minutos' => $minutosRestantes,
                'segundos' => $segundosRestantes
            ]);
        }
        
        if ($usuario->blocked_until && Carbon::now()->gte($usuario->blocked_until)) {
            $usuario->estado = 'activo';
            $usuario->login_attempts = 0;
            $usuario->blocked_until = null;
            $usuario->save();
        }
        
        return response()->json(['blocked' => false]);
    }
    
    public function cambiarContrasena(Request $request)
    {
        $request->validate([
            'contrasena_actual' => 'required|string',
            'contrasena_nueva' => 'required|string|min:8|confirmed',
        ], [
            'contrasena_nueva.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'contrasena_nueva.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ]);
        
        $user = $request->user();
        
        if (!Hash::check($request->contrasena_actual, $user->contrasena_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta.'
            ], 400);
        }
        
        $user->contrasena_hash = Hash::make($request->contrasena_nueva);
        $user->save();
        
        \App\Services\AuditLogService::registrar($user->id_usuario, 'Cambió su contraseña', $request);
        
        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente. Vuelve a iniciar sesión.'
        ]);
    }
}