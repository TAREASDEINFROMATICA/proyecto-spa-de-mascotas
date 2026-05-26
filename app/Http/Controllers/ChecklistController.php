<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\ChecklistItem;
use App\Models\FichaChecklist;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChecklistController extends Controller
{
    private function getUserFromToken(Request $request)
    {
        $token = $request->query('token') ?? $request->input('token');
        if (!$token) return null;
        $token = trim($token, "'\"");
        $tokenRecord = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if (!$tokenRecord) return null;
        return \App\Models\Usuario::find($tokenRecord->tokenable_id);
    }

    public function index(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return redirect('/')->with('error', 'No autorizado');
        }

        $token = $request->query('token');
        
        $empleado = Empleado::where('id_usuario', $user->id_usuario)->first();
        
        if (!$empleado) {
            return redirect('/groomer/dashboard?token=' . $token)
                ->with('error', 'No se encontró tu información de empleado');
        }
        
        $citas = Cita::where('id_empleado', $empleado->id_empleado)
            ->where('estado', 'programado')
            ->with(['mascota', 'servicio'])
            ->orderBy('fecha', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get();
        
        return view('personal.groomer.checklist', compact('citas', 'token', 'empleado'));
    }

    public function getChecklist(Request $request, $citaId)
    {
        try {
            $user = $this->getUserFromToken($request);
            if (!$user || $user->rol->nombre !== 'Groomer') {
                return response()->json(['error' => 'No autorizado'], 401);
            }
            
            $cita = Cita::with(['servicio', 'mascota'])->findOrFail($citaId);
            
            $empleado = Empleado::where('id_usuario', $user->id_usuario)->first();
            if ($cita->id_empleado != $empleado->id_empleado) {
                return response()->json(['error' => 'Esta cita no te pertenece'], 403);
            }
            
            $items = ChecklistItem::whereHas('servicios', function($q) use ($cita) {
                $q->where('servicio_checklist.id_servicio', $cita->id_servicio);
            })->get();
            
            $progreso = FichaChecklist::where('id_cita', $citaId)
                ->where('id_empleado', $empleado->id_empleado)
                ->get()
                ->keyBy('id_item');
            
            return response()->json([
                'success' => true,
                'cita' => $cita,
                'items' => $items,
                'progreso' => $progreso
            ]);
        } catch (\Exception $e) {
            Log::error('Error en getChecklist: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function guardarProgreso(Request $request, $citaId)
    {
        try {
            $user = $this->getUserFromToken($request);
            if (!$user || $user->rol->nombre !== 'Groomer') {
                return response()->json(['error' => 'No autorizado'], 401);
            }
            
            $request->validate([
                'id_item' => 'required|exists:checklist_items,id_item',
                'realizado' => 'required|boolean',
                'observacion' => 'nullable|string|max:500'
            ]);
            
            $empleado = Empleado::where('id_usuario', $user->id_usuario)->first();
            
            $cita = Cita::where('id_cita', $citaId)
                ->where('id_empleado', $empleado->id_empleado)
                ->first();
                
            if (!$cita) {
                return response()->json(['error' => 'Cita no encontrada o no te pertenece'], 404);
            }
            
            FichaChecklist::updateOrCreate(
                [
                    'id_cita' => $citaId,
                    'id_empleado' => $empleado->id_empleado,
                    'id_item' => $request->id_item
                ],
                [
                    'realizado' => $request->realizado,
                    'observacion' => $request->observacion,
                    'fecha_registro' => Carbon::now()
                ]
            );
            
            $totalItems = ChecklistItem::whereHas('servicios', function($q) use ($cita) {
                $q->where('servicio_checklist.id_servicio', $cita->id_servicio);
            })->count();
            
            $itemsCompletados = FichaChecklist::where('id_cita', $citaId)
                ->where('id_empleado', $empleado->id_empleado)
                ->where('realizado', true)
                ->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Progreso guardado',
                'completado' => $itemsCompletados,
                'total' => $totalItems
            ]);
        } catch (\Exception $e) {
            Log::error('Error en guardarProgreso: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function completarChecklist(Request $request, $citaId)
    {
        try {
            $user = $this->getUserFromToken($request);
            if (!$user || $user->rol->nombre !== 'Groomer') {
                return response()->json(['error' => 'No autorizado'], 401);
            }
            
            $empleado = Empleado::where('id_usuario', $user->id_usuario)->first();
            
            $cita = Cita::where('id_cita', $citaId)
                ->where('id_empleado', $empleado->id_empleado)
                ->first();
                
            if (!$cita) {
                return response()->json(['error' => 'Cita no encontrada'], 404);
            }
            
            $cita->estado = 'checklist_completado';
            $cita->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Checklist completado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en completarChecklist: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}