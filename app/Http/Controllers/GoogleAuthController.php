<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $usuario = Usuario::where('correo', $googleUser->getEmail())->first();
            
            if ($usuario) {
                if ($usuario->estado !== 'activo') {
                    return redirect('/')->with('error', '❌ Tu cuenta está ' . $usuario->estado);
                }
            }
            
            if (!$usuario) {
                $rolCliente = Rol::where('nombre', 'Cliente')->first();
                
                $usuario = Usuario::create([
                    'id_rol' => $rolCliente->id_rol,
                    'nombres' => $googleUser->getName(),
                    'apellidos' => '',
                    'correo' => $googleUser->getEmail(),
                    'contrasena_hash' => Hash::make('12345678'),
                    'telefono' => null,
                    'estado' => 'activo',
                    'email_verified_at' => Carbon::now(),
                    'fecha_registro' => Carbon::now(),
                ]);
                
                Cliente::create([
                    'id_usuario' => $usuario->id_usuario,
                    'direccion' => null,
                ]);
            }
            
            $token = $usuario->createToken('auth_token')->plainTextToken;
            
            // =========================================================
            // REDIRIGIR DIRECTAMENTE AL DASHBOARD CON HTML/JS
            // =========================================================
            return "
                <html>
                <head>
                    <script>
                        localStorage.setItem('token', '{$token}');
                        localStorage.setItem('user', JSON.stringify({ usuario: '{$usuario->nombres}' }));
                        window.location.href = '/cliente/dashboard?token={$token}';
                    </script>
                </head>
                <body>
                    <p>Redirigiendo...</p>
                </body>
                </html>
            ";
            
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Error al autenticar con Google: ' . $e->getMessage());
        }
    }
}