<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerfilClienteController extends Controller
{
    public function edit(Request $request)
    {
        // El usuario viene desde la ruta, no desde auth()
        $token = request()->query('token');
        $tokenRecord = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = Usuario::find($tokenRecord->tokenable_id);
        $cliente = $user->cliente;
        
        return view('cliente.perfil', compact('user', 'cliente', 'token'));
    }

    public function update(Request $request, $user = null)
{
    // Si no se pasó el usuario, obtenerlo del token
    if (!$user) {
        $token = $request->query('token') ?? $request->input('token');
        $tokenRecord = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = Usuario::find($tokenRecord->tokenable_id);
    }
    
    $request->validate([
        'nombres' => 'required|string|max:80|regex:/^[\pL\sáéíóúüñÁÉÍÓÚÜÑ]+$/u',
        'apellidos' => 'required|string|max:80|regex:/^[\pL\sáéíóúüñÁÉÍÓÚÜÑ]+$/u',
        'telefono' => 'required|string|regex:/^[0-9]{8,15}$/',
        'direccion' => 'nullable|string|max:200',
        'ci' => 'nullable|string|max:20|regex:/^[0-9]{6,12}$/|unique:usuarios,ci,' . $user->id_usuario . ',id_usuario',
    ]);
    
    // Actualizar usuario
    $user->nombres = $request->nombres;
    $user->apellidos = $request->apellidos;
    $user->telefono = $request->telefono;
    $user->ci = $request->ci;
    $user->save();
    
    // Actualizar dirección
    if ($user->cliente) {
        $user->cliente->direccion = $request->direccion;
        $user->cliente->save();
    }
    
    \App\Services\AuditLogService::registrar($user->id_usuario, 'Actualizó su perfil', $request);
    
    $token = $request->query('token') ?? $request->input('token');
    return redirect('/cliente/perfil?token=' . $token)->with('success', '✅ Datos actualizados correctamente');
}
}