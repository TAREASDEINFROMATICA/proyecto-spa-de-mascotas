<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;  // ← AGREGAR
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\AuditLogService;
use App\Mail\VerificacionEmail;  // ← AGREGAR
use Illuminate\Http\Request;
class RegistroController extends Controller
{
    public function showForm()
    {
        return view('cliente.registro');
    }
    
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Generar token de verificación (expira en 15 min)
            $verificationToken = Str::random(64);
            $tokenExpiresAt = Carbon::now()->addMinutes(15);
            
            // Obtener rol Cliente
            $rolCliente = Rol::where('nombre', 'Cliente')->first();
            
            // Crear usuario
            $usuario = Usuario::create([
                'id_rol' => $rolCliente->id_rol,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'correo' => $request->correo,
                'contrasena_hash' => Hash::make($request->contrasena),
                'telefono' => $request->telefono,
                'estado' => 'inactivo',
                'verification_token' => $verificationToken,
                'verification_token_expires_at' => $tokenExpiresAt,
                'fecha_registro' => Carbon::now(),
            ]);
            
            AuditLogService::registrar(
                $usuario->id_usuario,
                'Registro de nuevo cliente: ' . $request->nombres . ' ' . $request->apellidos,
                $request
            );
            
            // Crear registro de cliente
            $usuario->cliente()->create([
                'direccion' => $request->direccion,
            ]);
            
            // =========================================================
            // ENVIAR EMAIL DE VERIFICACIÓN (LO QUE FALTABA)
            // =========================================================
            Mail::to($usuario->correo)->send(new VerificacionEmail($usuario, $verificationToken));
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => '¡Registro exitoso! Revisa tu correo para activar tu cuenta (el enlace expira en 15 minutos).'
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error en el registro: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function verifyEmail($token)
    {
        $usuario = Usuario::where('verification_token', $token)
            ->where('verification_token_expires_at', '>', Carbon::now())
            ->first();
        
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado. Solicita un nuevo enlace de verificación.'
            ], 400);
        }
        
        $usuario->update([
            'email_verified_at' => Carbon::now(),
            'estado' => 'activo',
            'verification_token' => null,
            'verification_token_expires_at' => null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => '¡Email verificado exitosamente! Ya puedes iniciar sesión.'
        ]);
    }
    public function reenviarVerificacion(Request $request)
{
    $request->validate([
        'correo' => 'required|email|exists:usuarios,correo',
    ]);
    
    $usuario = Usuario::where('correo', $request->correo)->first();
    
    // Verificar si ya está activo
    if ($usuario->email_verified_at) {
        return response()->json([
            'success' => false,
            'message' => 'Esta cuenta ya está verificada. Puedes iniciar sesión.'
        ], 400);
    }
    
    // Verificar si el token actual aún es válido (menos de 15 min)
    if ($usuario->verification_token_expires_at && Carbon::now()->lt($usuario->verification_token_expires_at)) {
        $minutosRestantes = Carbon::now()->diffInMinutes($usuario->verification_token_expires_at);
        return response()->json([
            'success' => false,
            'message' => "Ya tienes un enlace de verificación activo. Puedes reenviarlo después de {$minutosRestantes} minutos."
        ], 400);
    }
    
    // Generar nuevo token
    $nuevoToken = Str::random(64);
    $usuario->verification_token = $nuevoToken;
    $usuario->verification_token_expires_at = Carbon::now()->addMinutes(15);
    $usuario->save();
    
    // Reenviar email
    Mail::to($usuario->correo)->send(new VerificacionEmail($usuario, $nuevoToken));
    
    return response()->json([
        'success' => true,
        'message' => 'Se ha enviado un nuevo enlace de verificación a tu correo. Válido por 15 minutos.'
    ]);
}
}