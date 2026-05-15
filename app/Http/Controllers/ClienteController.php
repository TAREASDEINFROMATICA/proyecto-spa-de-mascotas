<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mascota;

class ClienteController extends Controller
{
   public function index(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
        return redirect('/');
    }
    
    $rol = $user->esAdmin() ? 'admin' : 'recepcion';
    
    $clientes = Usuario::where('id_rol', 4)->with('cliente')->get();
    $token = $request->query('token');
    
    return view('personal.recepcion.clientes', compact('clientes', 'token', 'rol'));
}

    public function desactivar($id)
    {
        $cliente = Usuario::findOrFail($id);
        
        if ($cliente->id_rol != 4) {
            return back()->with('error', 'No es un cliente');
        }
        
        $cliente->estado = 'inactivo';
        $cliente->save();
        
        // FORMA CORRECTA de obtener el ID del usuario autenticado
        $userId = Auth::id();  // ← ESTO ES LO CORRECTO
        
        AuditLogService::registrar(
            $userId,
            'Desactivó cliente: ' . $cliente->nombres . ' ' . $cliente->apellidos,
            request()
        );
        
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente desactivado correctamente');
    }

    public function activar($id)
    {
        $cliente = Usuario::findOrFail($id);
        
        if ($cliente->id_rol != 4) {
            return back()->with('error', 'No es un cliente');
        }
        
        $cliente->estado = 'activo';
        $cliente->save();
        
        // FORMA CORRECTA de obtener el ID del usuario autenticado
        $userId = Auth::id();  // ← ESTO ES LO CORRECTO
        
        AuditLogService::registrar(
            $userId,
            'Activó cliente: ' . $cliente->nombres . ' ' . $cliente->apellidos,
            request()
        );
        
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente activado correctamente');
    }
    /**
 * Mostrar detalle de un cliente (para recepción/admin)
 */
public function show(Request $request, $id)
{
    $user = $this->getUserFromToken($request);
    if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
        return redirect('/');
    }
    
    $cliente = Usuario::with(['cliente', 'rol'])->findOrFail($id);
    $mascotas = Mascota::where('id_cliente', $cliente->cliente->id_cliente)->get();
    
    $token = $request->query('token');
    
    return view('personal.recepcion.cliente-detalle', compact('cliente', 'mascotas', 'token'));
}

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
 * Ver mascotas de un cliente específico
 */
public function mascotas(Request $request, $id)
{
    $user = $this->getUserFromToken($request);
    if (!$user || !($user->esAdmin() || $user->rol->nombre === 'Recepcion')) {
        return redirect('/');
    }
    
    $cliente = Usuario::findOrFail($id);
    $mascotas = Mascota::where('id_cliente', $cliente->cliente->id_cliente)->get();
    
    $token = $request->query('token');
    
    return view('personal.recepcion.mascotas-cliente', compact('cliente', 'mascotas', 'token'));
}

}