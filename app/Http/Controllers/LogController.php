<?php

namespace App\Http\Controllers;

use App\Models\LogSistema;
use App\Models\Usuario;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = LogSistema::with('usuario.rol');
        
        // Filtros
        if ($request->buscar) {
            $query->where('accion', 'LIKE', '%' . $request->buscar . '%');
        }
        
        if ($request->usuario) {
            $query->where('id_usuario', $request->usuario);
        }
        
        $logs = $query->orderBy('id_log', 'desc')->paginate(50);
        $usuarios = Usuario::with('rol')->get();
        
        return view('admin.logs.index', compact('logs', 'usuarios'));
    }
}