<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Mascota;
use App\Models\Cliente;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MascotaController extends Controller
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
        
        return Usuario::find($tokenRecord->tokenable_id);
    }

    /**
     * Listar mascotas
     */
    public function index(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return redirect('/');
        }
        
        if ($user->esAdmin()) {
            $mascotas = Mascota::with('cliente.usuario')->get();
            return view('admin.mascotas.index', compact('mascotas'));
        }
        
        $cliente = $user->cliente;
        if (!$cliente) {
            return redirect('/')->with('error', 'No tienes mascotas registradas.');
        }
        
        $mascotas = Mascota::where('id_cliente', $cliente->id_cliente)
            ->orderBy('nombre')
            ->get();
        
        return view('cliente.mascotas.index', compact('mascotas'));
    }

    /**
     * Formulario para crear mascota
     */
    public function create(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return redirect('/');
        }
        
        $temperamentos = ['tranquilo', 'nervioso', 'agresivo', 'miedoso', 'jugueton', 'otro'];
        $especies = ['Perro', 'Gato', 'Conejo', 'Hamster', 'Ave', 'Otro'];
        
        $clientes = null;
        if ($user->esAdmin()) {
            $clientes = Cliente::with('usuario')->get();
        } else {
            $clientes = collect([$user->cliente]);
        }
        
        return view('cliente.mascotas.create', compact('temperamentos', 'especies', 'clientes'));
    }

    /**
     * Guardar nueva mascota
     */
    public function store(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return redirect('/')->with('error', 'No autorizado');
        }
        
        $request->validate([
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'nombre' => 'required|string|max:80',
            'especie' => 'required|string|max:40',
            'raza' => 'nullable|string|max:60',
            'sexo' => 'nullable|string|max:15',
            'fecha_nacimiento' => 'nullable|date',
            'peso' => 'nullable|numeric|min:0|max:200',
            'color' => 'nullable|string|max:50',
            'temperamento_general' => 'nullable|string',
            'alergias' => 'nullable|string',
            'cuidados_especiales' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        
        try {
            $data = $request->except('foto', 'token');
            
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $nombrePersonalizado = time() . '_' . str_replace(' ', '_', $request->nombre) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('mascotas', $nombrePersonalizado, 'public');
                $data['foto'] = $path;
            }
            
            $mascota = Mascota::create($data);
            
            AuditLogService::registrar(
                $user->id_usuario,
                'Registró mascota: ' . $mascota->nombre,
                $request
            );
            
            DB::commit();
            
            $token = $request->query('token') ?? $request->input('token');
            $redirect = $user->esAdmin() ? '/admin/mascotas' : '/cliente/mascotas';
            return redirect($redirect . '?token=' . $token)->with('success', '✅ Mascota registrada correctamente');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalles de mascota
     */
    public function show(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return redirect('/');
        }
        
        $mascota = Mascota::with('cliente.usuario')->findOrFail($id);
        
        if (!$user->esAdmin() && $mascota->cliente->id_usuario !== $user->id_usuario) {
            abort(403, 'No tienes permiso para ver esta mascota.');
        }
        
        return view('cliente.mascotas.show', compact('mascota'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return redirect('/');
        }
        
        $mascota = Mascota::findOrFail($id);
        
        if (!$user->esAdmin() && $mascota->cliente->id_usuario !== $user->id_usuario) {
            abort(403, 'No tienes permiso para editar esta mascota.');
        }
        
        $temperamentos = ['tranquilo', 'nervioso', 'agresivo', 'miedoso', 'jugueton', 'otro'];
        $especies = ['Perro', 'Gato', 'Conejo', 'Hamster', 'Ave', 'Otro'];
        
        return view('cliente.mascotas.edit', compact('mascota', 'temperamentos', 'especies'));
    }

    /**
     * Actualizar mascota
     */
    public function update(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return redirect('/')->with('error', 'No autorizado');
        }
        
        $mascota = Mascota::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:80',
            'especie' => 'required|string|max:40',
            'raza' => 'nullable|string|max:60',
            'sexo' => 'nullable|string|max:15',
            'fecha_nacimiento' => 'nullable|date',
            'peso' => 'nullable|numeric|min:0|max:200',
            'color' => 'nullable|string|max:50',
            'temperamento_general' => 'nullable|string',
            'alergias' => 'nullable|string',
            'cuidados_especiales' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        
        try {
            if ($request->hasFile('foto')) {
                if ($mascota->foto) {
                    Storage::disk('public')->delete($mascota->foto);
                }
                $file = $request->file('foto');
                $nombrePersonalizado = time() . '_' . str_replace(' ', '_', $request->nombre) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('mascotas', $nombrePersonalizado, 'public');
                $mascota->foto = $path;
            }
            
            $mascota->fill($request->except('foto', 'token'));
            $mascota->save();
            
            AuditLogService::registrar(
                $user->id_usuario,
                'Actualizó mascota: ' . $mascota->nombre,
                $request
            );
            
            DB::commit();
            
            $token = $request->query('token') ?? $request->input('token');
            return redirect('/cliente/mascotas/' . $id . '?token=' . $token)->with('success', '✅ Mascota actualizada correctamente');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Desactivar mascota
     */
    public function destroy(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return redirect('/')->with('error', 'No autorizado');
        }
        
        $mascota = Mascota::findOrFail($id);
        
        if (!$user->esAdmin() && $mascota->cliente->id_usuario !== $user->id_usuario) {
            abort(403, 'No tienes permiso para eliminar esta mascota.');
        }
        
        $mascota->estado = 'inactiva';
        $mascota->save();
        
        AuditLogService::registrar(
            $user->id_usuario,
            'Desactivó mascota: ' . $mascota->nombre,
            $request
        );
        
        $token = $request->query('token') ?? $request->input('token');
        $redirect = $user->esAdmin() ? '/admin/mascotas' : '/cliente/mascotas';
        return redirect($redirect . '?token=' . $token)->with('success', '✅ Mascota desactivada correctamente');
    }
    
    /**
     * Activar mascota (solo admin)
     */
    public function activate(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user || !$user->esAdmin()) {
            abort(403);
        }
        
        $mascota = Mascota::findOrFail($id);
        $mascota->estado = 'activa';
        $mascota->save();
        
        AuditLogService::registrar(
            $user->id_usuario,
            'Activó mascota: ' . $mascota->nombre,
            $request
        );
        
        $token = $request->query('token') ?? $request->input('token');
        return redirect('/admin/mascotas?token=' . $token)->with('success', '✅ Mascota activada correctamente');
    }
}