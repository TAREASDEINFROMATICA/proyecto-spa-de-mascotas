<?php

namespace App\Http\Controllers;

use App\Models\DiaNoLaborable;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DiaNoLaborableController extends Controller
{
    // Validar que sea Admin
    private function verificarAdmin(Request $request)
    {
        $token = $request->query('token');
        if (!$token) {
            return redirect('/');
        }
        
        $tokenRecord = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if (!$tokenRecord) {
            return redirect('/');
        }
        
        $user = \App\Models\Usuario::find($tokenRecord->tokenable_id);
        
        if (!$user || $user->rol->nombre !== 'Administrador') {
            return redirect('/');
        }
        
        return $user;
    }

    // Listar días no laborables
    public function index(Request $request)
    {
        $user = $this->verificarAdmin($request);
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $dias = DiaNoLaborable::orderBy('fecha', 'desc')->paginate(15);
        $tipos = DiaNoLaborable::tipos();
        $token = $request->query('token');
        
        return view('admin.dias-no-laborables.index', compact('dias', 'tipos', 'token'));
    }

    // Formulario crear
    public function create(Request $request)
    {
        $user = $this->verificarAdmin($request);
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $tipos = DiaNoLaborable::tipos();
        $token = $request->query('token');
        
        return view('admin.dias-no-laborables.create', compact('tipos', 'token'));
    }

    // Guardar nuevo día no laborable
    public function store(Request $request)
    {
        $user = $this->verificarAdmin($request);
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $request->validate([
            'fecha' => 'required|date|unique:dias_no_laborables,fecha',
            'tipo' => 'required|in:feriado,mantenimiento,ausencia,descanso',
            'motivo' => 'nullable|string|max:200'
        ]);
        
        $dia = DiaNoLaborable::create($request->all());
        
        AuditLogService::registrar(
            $user->id_usuario,
            "Creó día no laborable: {$dia->fecha} - {$dia->tipo}",
            $request
        );
        
        $token = $request->query('token');
        return redirect("/admin/dias-no-laborables?token={$token}")
            ->with('success', '✅ Día no laborable agregado correctamente');
    }

    // Formulario editar
    public function edit(Request $request, $id)
    {
        $user = $this->verificarAdmin($request);
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $dia = DiaNoLaborable::findOrFail($id);
        $tipos = DiaNoLaborable::tipos();
        $token = $request->query('token');
        
        return view('admin.dias-no-laborables.edit', compact('dia', 'tipos', 'token'));
    }

    // Actualizar
    public function update(Request $request, $id)
    {
        $user = $this->verificarAdmin($request);
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $dia = DiaNoLaborable::findOrFail($id);
        
        $request->validate([
            'fecha' => 'required|date|unique:dias_no_laborables,fecha,' . $id . ',id_dia_no_laborable',
            'tipo' => 'required|in:feriado,mantenimiento,ausencia,descanso',
            'motivo' => 'nullable|string|max:200'
        ]);
        
        $dia->update($request->all());
        
        AuditLogService::registrar(
            $user->id_usuario,
            "Actualizó día no laborable: {$dia->fecha}",
            $request
        );
        
        $token = $request->query('token');
        return redirect("/admin/dias-no-laborables?token={$token}")
            ->with('success', '✅ Día no laborable actualizado');
    }

    // Eliminar (soft delete o hard delete)
    public function destroy(Request $request, $id)
    {
        $user = $this->verificarAdmin($request);
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        $dia = DiaNoLaborable::findOrFail($id);
        $fecha = $dia->fecha;
        $dia->delete();
        
        AuditLogService::registrar(
            $user->id_usuario,
            "Eliminó día no laborable: {$fecha}",
            $request
        );
        
        return response()->json(['success' => true, 'message' => 'Día eliminado correctamente']);
    }

    // Cambiar estado (activar/desactivar)
    public function toggleEstado(Request $request, $id)
    {
        $user = $this->verificarAdmin($request);
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        $dia = DiaNoLaborable::findOrFail($id);
        $dia->estado = $dia->estado === 'activo' ? 'inactivo' : 'activo';
        $dia->save();
        
        return response()->json([
            'success' => true, 
            'estado' => $dia->estado,
            'message' => 'Estado actualizado'
        ]);
    }
}