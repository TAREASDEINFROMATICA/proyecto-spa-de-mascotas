<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{
    /**
     * Obtener usuario desde el token
     */
    private function getUserFromToken(Request $request)
    {
        $token = $request->query('token') ?? $request->input('token');
        
        if (!$token) {
            return null;
        }
        
        $token = trim($token, "'\"");
        
        $tokenRecord = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if (!$tokenRecord) {
            return null;
        }
        
        return \App\Models\Usuario::find($tokenRecord->tokenable_id);
    }

    /**
     * Listar servicios
     */
    public function index(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user || !$user->esAdmin()) {
            return redirect('/');
        }
        
        $servicios = Servicio::orderBy('nombre')->get();
        $token = $request->query('token');
        
        return view('admin.servicios.index', compact('servicios', 'token'));
    }

    /**
     * Formulario para crear servicio
     */
    public function create(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user || !$user->esAdmin()) {
            return redirect('/');
        }
        
        $tiposMascota = ['Perro', 'Gato', 'Ambos', 'Otro'];
        $token = $request->query('token');
        
        return view('admin.servicios.create', compact('tiposMascota', 'token'));
    }

    /**
     * Guardar nuevo servicio
     */
    public function store(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user || !$user->esAdmin()) {
            return redirect('/')->with('error', 'No autorizado');
        }
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'duracion_minutos' => 'required|integer|min:5|max:480',
            'precio' => 'required|numeric|min:0',
            'tipo_mascota' => 'nullable|string|max:40',
        ]);
        
        $servicio = Servicio::create($request->all());
        
        AuditLogService::registrar(
            $user->id_usuario,
            'Creó servicio: ' . $servicio->nombre,
            $request
        );
        
        $token = $request->query('token');
        return redirect('/admin/servicios?token=' . $token)
            ->with('success', '✅ Servicio creado correctamente');
    }

    /**
     * Formulario de edición
     */
    public function edit(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user || !$user->esAdmin()) {
            return redirect('/');
        }
        
        $servicio = Servicio::findOrFail($id);
        $tiposMascota = ['Perro', 'Gato', 'Ambos', 'Otro'];
        $token = $request->query('token');
        
        return view('admin.servicios.edit', compact('servicio', 'tiposMascota', 'token'));
    }

    /**
     * Actualizar servicio
     */
    public function update(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user || !$user->esAdmin()) {
            return redirect('/')->with('error', 'No autorizado');
        }
        
        $servicio = Servicio::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'duracion_minutos' => 'required|integer|min:5|max:480',
            'precio' => 'required|numeric|min:0',
            'tipo_mascota' => 'nullable|string|max:40',
        ]);
        
        $servicio->update($request->all());
        
        AuditLogService::registrar(
            $user->id_usuario,
            'Actualizó servicio: ' . $servicio->nombre,
            $request
        );
        
        $token = $request->query('token');
        return redirect('/admin/servicios?token=' . $token)
            ->with('success', '✅ Servicio actualizado correctamente');
    }

    /**
     * Desactivar servicio
     */
    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->estado = 'inactivo';
        $servicio->save();
        
        $token = request()->query('token');
        return redirect('/admin/servicios?token=' . $token)
            ->with('success', '✅ Servicio desactivado correctamente');
    }

   /**
 * Activar servicio
 */
public function activate($id)
{
    $servicio = Servicio::findOrFail($id);
    $servicio->estado = 'activo';
    $servicio->save();
    
    $token = request()->query('token');
    return redirect('/admin/servicios?token=' . $token)
        ->with('success', '✅ Servicio activado correctamente');
}
    /**
 * Desactivar servicio
 */
public function desactivate($id)
{
    $servicio = Servicio::findOrFail($id);
    $servicio->estado = 'inactivo';
    $servicio->save();
    
    $token = request()->query('token');
    return redirect('/admin/servicios?token=' . $token)
        ->with('success', '✅ Servicio desactivado correctamente');
}
}