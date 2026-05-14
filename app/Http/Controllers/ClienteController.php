<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Usuario::where('id_rol', 4)
            ->with('cliente')
            ->orderBy('id_usuario', 'desc')
            ->get();
        
        return view('admin.clientes.index', compact('clientes'));
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
}