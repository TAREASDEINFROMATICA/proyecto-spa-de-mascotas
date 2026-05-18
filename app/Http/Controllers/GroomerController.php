<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mascota;
use App\Models\Servicio;
use App\Models\Empleado;
use App\Models\FotoMascota;
use App\Models\FichaTecnica;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;
use App\Models\InsumoTratamiento;
use App\Models\ConsumoInsumoCita;
use App\Models\Calificacion;
use Illuminate\Support\Facades\DB;

class GroomerController extends Controller
{
    private function getUserFromToken(Request $request)
    {
        $token = $request->query('token') ?? $request->input('token');
        if (!$token) return null;
        $token = trim($token, "'\"");
        $tokenRecord = PersonalAccessToken::findToken($token);
        if (!$tokenRecord) return null;
        return \App\Models\Usuario::find($tokenRecord->tokenable_id);
    }

    private function getEmpleadoId($user)
    {
        $empleado = Empleado::where('id_usuario', $user->id_usuario)->first();
        return $empleado ? $empleado->id_empleado : null;
    }

    // =========================================================
    // MIS CITAS
    // =========================================================
    public function misCitas(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return redirect('/');
        }
        
        $empleadoId = $this->getEmpleadoId($user);
        $citas = Cita::where('id_empleado', $empleadoId)
            ->whereIn('estado', ['programado', 'reservado', 'concluido'])
            ->with(['mascota', 'servicio'])
            ->orderBy('fecha', 'desc')
            ->get();
        
        $token = $request->query('token');
        
        return view('personal.groomer.mis-citas', compact('citas', 'token'));
    }

    // =========================================================
    // MIS MASCOTAS ASIGNADAS
    // =========================================================
    public function misMascotas(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return redirect('/');
        }
        
        $empleadoId = $this->getEmpleadoId($user);
        $citas = Cita::where('id_empleado', $empleadoId)
            ->where('estado', 'programado')
            ->with(['mascota'])
            ->get();
        
        $mascotasIds = $citas->pluck('id_mascota')->unique();
        $mascotas = Mascota::whereIn('id_mascota', $mascotasIds)->get();
        
        $token = $request->query('token');
        
        return view('personal.groomer.mis-mascotas', compact('mascotas', 'token'));
    }

    // =========================================================
    // CHECKLIST
    // =========================================================
    public function checklist(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return redirect('/');
        }
        
        $token = $request->query('token');
        
        return view('personal.groomer.checklist', compact('token'));
    }

    // =========================================================
    // GALERÍA DE FOTOS
    // =========================================================
    public function galeria(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return redirect('/');
        }
        
        $empleadoId = $this->getEmpleadoId($user);
        $citas = Cita::where('id_empleado', $empleadoId)
            ->with(['mascota', 'fichaTecnica'])
            ->get();
        
        $fotos = collect();
        foreach ($citas as $cita) {
            if ($cita->fichaTecnica) {
                $fotosMascota = FotoMascota::where('id_ficha', $cita->fichaTecnica->id_ficha)->get();
                $fotos = $fotos->merge($fotosMascota);
            }
        }
        
        $token = $request->query('token');
        
        return view('personal.groomer.galeria', compact('fotos', 'token'));
    }

    // =========================================================
    // INSUMOS
    // =========================================================
    public function insumos(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return redirect('/');
        }
        
        $token = $request->query('token');
        
        return view('personal.groomer.insumos', compact('token'));
    }

    // =========================================================
    // FICHA TÉCNICA (SOLO LECTURA)
    // =========================================================
    public function fichaTecnicaVer(Request $request, $citaId)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return redirect('/');
        }
        
        $empleadoId = $this->getEmpleadoId($user);
        $cita = Cita::where('id_empleado', $empleadoId)
            ->where('id_cita', $citaId)
            ->with(['mascota', 'servicio', 'fichaTecnica'])
            ->firstOrFail();
        
        // Obtener fotos SOLO de esta ficha técnica
        $fotosAntes = FotoMascota::where('id_ficha', $cita->fichaTecnica->id_ficha ?? 0)
            ->where('tipo', 'antes')
            ->get();
        
        $fotosDespues = FotoMascota::where('id_ficha', $cita->fichaTecnica->id_ficha ?? 0)
            ->where('tipo', 'despues')
            ->get();
        
        $token = $request->query('token');
        
        // Parsear observaciones
        $observaciones = $cita->observaciones ?? '';
        
        $checklistRealizado = [];
        if (preg_match('/=== CHECKLIST REALIZADO ===\n(.*?)(\n\n|$)/s', $observaciones, $matches)) {
            $checklistRealizado = explode(', ', $matches[1]);
        }
        
        $recomendaciones = '';
        if (preg_match('/=== RECOMENDACIONES ===\n(.*?)(\n\n|$)/s', $observaciones, $matches)) {
            $recomendaciones = $matches[1];
        }
        
        $estadoIngreso = '';
        if (preg_match('/=== ESTADO DE INGRESO ===\n(.*?)(\n\n|$)/s', $observaciones, $matches)) {
            $estadoIngreso = $matches[1];
        }
        
        $observacionesExtra = '';
        if (preg_match('/=== OBSERVACIONES ===\n(.*?)(\n\n|$)/s', $observaciones, $matches)) {
            $observacionesExtra = $matches[1];
        }
        
        return view('personal.groomer.ficha-tecnica-ver', compact(
            'cita', 'fotosAntes', 'fotosDespues', 'token', 
            'checklistRealizado', 'recomendaciones', 'estadoIngreso', 'observacionesExtra'
        ));
    }

    // =========================================================
    // FICHA TÉCNICA (EDITABLE)
    // =========================================================
    public function fichaTecnica(Request $request, $citaId)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return redirect('/');
        }
        
        $empleadoId = $this->getEmpleadoId($user);
        $cita = Cita::where('id_empleado', $empleadoId)
            ->where('id_cita', $citaId)
            ->with(['mascota', 'servicio', 'fichaTecnica'])
            ->firstOrFail();
        
        // Si no tiene ficha técnica, la creamos
        if (!$cita->fichaTecnica) {
            $fichaTecnica = FichaTecnica::create([
                'id_cita' => $citaId,
                'estado_ingreso' => 'Pendiente',
                'fecha_apertura' => Carbon::now()
            ]);
            $cita->load('fichaTecnica');
        }
        
        // Obtener fotos SOLO de esta ficha técnica
        $fotosAntes = FotoMascota::where('id_ficha', $cita->fichaTecnica->id_ficha)
            ->where('tipo', 'antes')
            ->get();
        
        $fotosDespues = FotoMascota::where('id_ficha', $cita->fichaTecnica->id_ficha)
            ->where('tipo', 'despues')
            ->get();
        
        // Parsear datos guardados previamente
        $datosGuardados = [
            'estado_ingreso' => '',
            'checklist' => [],
            'observaciones' => '',
            'recomendaciones' => ''
        ];
        
        if ($cita->observaciones) {
            // Extraer ESTADO DE INGRESO
            if (preg_match('/=== ESTADO DE INGRESO ===\n(.*?)(\n\n|$)/s', $cita->observaciones, $matches)) {
                $datosGuardados['estado_ingreso'] = trim($matches[1]);
            }
            
            // Extraer CHECKLIST REALIZADO
            if (preg_match('/=== CHECKLIST REALIZADO ===\n(.*?)(\n\n|$)/s', $cita->observaciones, $matches)) {
                $checklistStr = trim($matches[1]);
                if ($checklistStr && $checklistStr !== 'Ninguno') {
                    $datosGuardados['checklist'] = explode(', ', $checklistStr);
                }
            }
            
            // Extraer OBSERVACIONES
            if (preg_match('/=== OBSERVACIONES ===\n(.*?)(\n\n|$)/s', $cita->observaciones, $matches)) {
                $datosGuardados['observaciones'] = trim($matches[1]);
            }
            
            // Extraer RECOMENDACIONES
            if (preg_match('/=== RECOMENDACIONES ===\n(.*?)(\n\n|$)/s', $cita->observaciones, $matches)) {
                $datosGuardados['recomendaciones'] = trim($matches[1]);
            }
        }
        
        $token = $request->query('token');
        
        return view('personal.groomer.ficha-tecnica', compact('cita', 'fotosAntes', 'fotosDespues', 'token', 'datosGuardados'));
    }

    // =========================================================
    // CERRAR SERVICIO
    // =========================================================
    public function cerrarServicio(Request $request, $citaId)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        $empleadoId = $this->getEmpleadoId($user);
        $cita = Cita::where('id_empleado', $empleadoId)
            ->where('id_cita', $citaId)
            ->first();
        
        if (!$cita) {
            return response()->json(['success' => false, 'message' => 'Cita no encontrada'], 404);
        }
        
        if ($cita->estado === 'concluido') {
            return response()->json(['success' => false, 'message' => 'Este servicio ya está cerrado'], 400);
        }
        
        $estadoIngreso = $request->input('estado_ingreso');
        $checklist = $request->input('checklist', []);
        $observaciones = $request->input('observaciones');
        $recomendaciones = $request->input('recomendaciones');
        
        $textoFinal = "=== ESTADO DE INGRESO ===\n" . $estadoIngreso . 
                      "\n\n=== CHECKLIST REALIZADO ===\n" . (count($checklist) > 0 ? implode(', ', $checklist) : 'Ninguno') . 
                      "\n\n=== OBSERVACIONES ===\n" . $observaciones . 
                      "\n\n=== RECOMENDACIONES ===\n" . $recomendaciones .
                      "\n\n=== FECHA CIERRE ===\n" . Carbon::now()->format('Y-m-d H:i:s');
        
        $cita->observaciones = $textoFinal;
        $cita->estado = 'concluido';
        $cita->save();
        
        // Actualizar ficha técnica con fecha de cierre
        if ($cita->fichaTecnica) {
            $cita->fichaTecnica->fecha_cierre = Carbon::now();
            $cita->fichaTecnica->save();
        }
        
        AuditLogService::registrar(
            $user->id_usuario,
            'Cerró servicio cita ID: ' . $citaId,
            $request
        );
        
        return response()->json(['success' => true, 'message' => 'Servicio cerrado correctamente']);
    }

    // =========================================================
    // SUBIR FOTO
    // =========================================================
    public function subirFotoDirecto(Request $request)
    {
        try {
            $citaId = $request->cita_id;
            $tipo = $request->tipo;
            $foto = $request->file('foto');
            
            if (!$foto) {
                return response()->json(['success' => false, 'message' => 'No hay foto']);
            }
            
            // Obtener la cita con la mascota
            $cita = Cita::with('mascota')->find($citaId);
            if (!$cita) {
                return response()->json(['success' => false, 'message' => 'Cita no encontrada']);
            }
            
            // Buscar o crear la FICHA TÉCNICA
            $fichaTecnica = FichaTecnica::firstOrCreate(
                ['id_cita' => $citaId],
                [
                    'estado_ingreso' => 'Pendiente',
                    'fecha_apertura' => Carbon::now()
                ]
            );
            
            // Crear nombre único para la foto (incluyendo cita_id)
            $nombrePersonalizado = 'cita_' . $citaId . '_' . time() . '_' . str_replace(' ', '_', $cita->mascota->nombre) . '_' . $tipo . '.' . $foto->getClientOriginalExtension();
            $path = $foto->storeAs('mascotas/galeria', $nombrePersonalizado, 'public');
            
            // Guardar en BD usando el id_ficha de la ficha técnica
            FotoMascota::create([
                'id_ficha' => $fichaTecnica->id_ficha,
                'url' => $path,
                'tipo' => $tipo
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Foto subida correctamente',
                'url' => Storage::url($path)
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // =========================================================
    // GUARDAR PROGRESO DE FICHA TÉCNICA (sin cerrar)
    // =========================================================
    public function guardarProgreso(Request $request, $citaId)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        $empleadoId = $this->getEmpleadoId($user);
        $cita = Cita::where('id_empleado', $empleadoId)
            ->where('id_cita', $citaId)
            ->first();
        
        if (!$cita) {
            return response()->json(['success' => false, 'message' => 'Cita no encontrada'], 404);
        }
        
        $estadoIngreso = $request->input('estado_ingreso');
        $checklist = $request->input('checklist', []);
        $observaciones = $request->input('observaciones');
        $recomendaciones = $request->input('recomendaciones');
        
        $textoGuardado = "=== ESTADO DE INGRESO ===\n" . $estadoIngreso . 
                         "\n\n=== CHECKLIST REALIZADO ===\n" . (count($checklist) > 0 ? implode(', ', $checklist) : 'Ninguno') . 
                         "\n\n=== OBSERVACIONES ===\n" . $observaciones . 
                         "\n\n=== RECOMENDACIONES ===\n" . $recomendaciones;
        
        $cita->observaciones = $textoGuardado;
        $cita->save();
        
        return response()->json(['success' => true, 'message' => 'Progreso guardado correctamente']);
    }


// =========================================================
// REGISTRAR CONSUMO DE INSUMOS
// =========================================================
// =========================================================
// REGISTRAR CONSUMO DE INSUMOS (GROOMER)
// =========================================================
public function registrarConsumoInsumo(Request $request, $citaId)
{
    try {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Groomer') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        $empleadoId = $this->getEmpleadoId($user);
        $cita = Cita::where('id_empleado', $empleadoId)
            ->where('id_cita', $citaId)
            ->first();
        
        if (!$cita) {
            return response()->json(['success' => false, 'message' => 'Cita no encontrada'], 404);
        }
        
        if ($cita->estado === 'concluido') {
            return response()->json(['success' => false, 'message' => 'Este servicio ya está cerrado'], 400);
        }
        
        $request->validate([
            'insumos' => 'required|array',
            'insumos.*.id_insumo' => 'required|exists:insumos_tratamiento,id_insumo',
            'insumos.*.cantidad' => 'required|numeric|min:0.01'
        ]);
        
        $resultados = [];
        
        foreach ($request->insumos as $item) {
            $insumo = InsumoTratamiento::find($item['id_insumo']);
            
            if (!$insumo) {
                $resultados[] = [
                    'id_insumo' => $item['id_insumo'],
                    'success' => false,
                    'message' => 'Insumo no encontrado'
                ];
                continue;
            }
            
            if ($insumo->stock < $item['cantidad']) {
                $resultados[] = [
                    'id_insumo' => $item['id_insumo'],
                    'success' => false,
                    'message' => "Stock insuficiente. Disponible: {$insumo->stock} {$insumo->unidad_medida}"
                ];
                continue;
            }
            
            // Registrar consumo
            ConsumoInsumoCita::create([
                'id_cita' => $citaId,
                'id_insumo' => $item['id_insumo'],
                'cantidad_usada' => $item['cantidad']
            ]);
            
            // Descontar stock
            $insumo->stock -= $item['cantidad'];
            $insumo->save();
            
            $resultados[] = [
                'id_insumo' => $item['id_insumo'],
                'success' => true,
                'message' => "Consumido {$item['cantidad']} {$insumo->unidad_medida} de {$insumo->nombre}"
            ];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Consumo registrado',
            'resultados' => $resultados
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
// =========================================================
// OBTENER INSUMOS DISPONIBLES
// =========================================================
public function getInsumosDisponibles(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user || $user->rol->nombre !== 'Groomer') {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }
    
    $insumos = InsumoTratamiento::where('estado', 'activo')
        ->where('stock', '>', 0)
        ->orderBy('nombre')
        ->get(['id_insumo', 'nombre', 'stock', 'unidad_medida']);
    
    return response()->json([
        'success' => true,
        'insumos' => $insumos
    ]);
}
// =========================================================
// OBTENER CONSUMOS DE UNA CITA
// =========================================================
public function getConsumosByCita(Request $request, $citaId)
{
    $user = $this->getUserFromToken($request);
    if (!$user || $user->rol->nombre !== 'Groomer') {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }
    
    $consumos = ConsumoInsumoCita::with('insumo')
        ->where('id_cita', $citaId)
        ->get();
    
    return response()->json([
        'success' => true,
        'consumos' => $consumos
    ]);
}
// =========================================================
// MIS CONSUMOS (HISTORIAL)
// =========================================================
public function misConsumos(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user || $user->rol->nombre !== 'Groomer') {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }
    
    $empleadoId = $this->getEmpleadoId($user);
    
    $query = ConsumoInsumoCita::with(['cita.mascota', 'cita.servicio', 'insumo'])
        ->whereHas('cita', function($q) use ($empleadoId) {
            $q->where('id_empleado', $empleadoId);
        });
    
    // Filtrar por fechas
    if ($request->fecha_inicio) {
        $query->whereHas('cita', function($q) use ($request) {
            $q->whereDate('fecha', '>=', $request->fecha_inicio);
        });
    }
    if ($request->fecha_fin) {
        $query->whereHas('cita', function($q) use ($request) {
            $q->whereDate('fecha', '<=', $request->fecha_fin);
        });
    }
    
    $consumos = $query->orderBy('id_consumo', 'desc')->get();
    
    $resultados = [];
    foreach ($consumos as $consumo) {
        $resultados[] = [
            'id_consumo' => $consumo->id_consumo,
            'fecha' => $consumo->cita->fecha ?? null,
            'mascota_nombre' => $consumo->cita->mascota->nombre ?? null,
            'servicio_nombre' => $consumo->cita->servicio->nombre ?? null,
            'insumo_nombre' => $consumo->insumo->nombre,
            'cantidad_usada' => $consumo->cantidad_usada,
            'unidad_medida' => $consumo->insumo->unidad_medida
        ];
    }
    
    return response()->json([
        'success' => true,
        'consumos' => $resultados
    ]);
}




// =========================================================
// DASHBOARD MEJORADO CON ESTADÍSTICAS
// =========================================================
public function dashboard(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user || $user->rol->nombre !== 'Groomer') {
        return redirect('/');
    }
    
    $empleadoId = $this->getEmpleadoId($user);
    $token = $request->query('token');
    
    // Estadísticas generales
    $totalCitas = Cita::where('id_empleado', $empleadoId)->count();
    $citasConcluidas = Cita::where('id_empleado', $empleadoId)->where('estado', 'concluido')->count();
    $citasPendientes = Cita::where('id_empleado', $empleadoId)->whereIn('estado', ['programado', 'reservado'])->count();
    $citasHoy = Cita::where('id_empleado', $empleadoId)
        ->where('fecha', now()->toDateString())
        ->whereIn('estado', ['programado', 'reservado'])
        ->count();
    
    // Calificación promedio
    $promedioCalificacion = Calificacion::whereHas('cita', function($q) use ($empleadoId) {
        $q->where('id_empleado', $empleadoId);
    })->avg('puntuacion');
    
    // Últimas calificaciones
    $ultimasCalificaciones = Calificacion::with(['cita.mascota', 'cita.servicio'])
        ->whereHas('cita', function($q) use ($empleadoId) {
            $q->where('id_empleado', $empleadoId);
        })
        ->orderBy('fecha_calificacion', 'desc')
        ->limit(5)
        ->get();
    
    // Citas de hoy
    $citasDelDia = Cita::where('id_empleado', $empleadoId)
        ->where('fecha', now()->toDateString())
        ->whereIn('estado', ['programado', 'reservado'])
        ->with(['mascota', 'servicio'])
        ->orderBy('hora_inicio')
        ->get();
    
    // Próximas citas
    $proximasCitas = Cita::where('id_empleado', $empleadoId)
        ->where('fecha', '>', now()->toDateString())
        ->whereIn('estado', ['programado', 'reservado'])
        ->with(['mascota', 'servicio'])
        ->orderBy('fecha')
        ->orderBy('hora_inicio')
        ->limit(10)
        ->get();
    
    // Alertas de stock bajo
    $stockBajo = \App\Models\InsumoTratamiento::where('estado', 'activo')
        ->whereColumn('stock', '<=', 'stock_minimo')
        ->get();
    
    return view('personal.groomer.dashboard', compact(
        'token', 'totalCitas', 'citasConcluidas', 'citasPendientes', 'citasHoy',
        'promedioCalificacion', 'ultimasCalificaciones', 'citasDelDia',
        'proximasCitas', 'stockBajo'
    ));
}

// =========================================================
// MIS CALIFICACIONES
// =========================================================
public function misCalificaciones(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user || $user->rol->nombre !== 'Groomer') {
        return redirect('/');
    }
    
    $empleadoId = $this->getEmpleadoId($user);
    $token = $request->query('token');
    
    $calificaciones = Calificacion::with(['cita.mascota', 'cita.servicio'])
        ->whereHas('cita', function($q) use ($empleadoId) {
            $q->where('id_empleado', $empleadoId);
        })
        ->orderBy('fecha_calificacion', 'desc')
        ->paginate(15);
    
    $promedio = $calificaciones->avg('puntuacion');
    $total = $calificaciones->total();
    
    return view('personal.groomer.mis-calificaciones', compact('token', 'calificaciones', 'promedio', 'total'));
}

// =========================================================
// EXPORTAR SERVICIOS A CSV
// =========================================================
public function exportarServiciosCSV(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user || $user->rol->nombre !== 'Groomer') {
        return redirect('/');
    }
    
    $empleadoId = $this->getEmpleadoId($user);
    
    $citas = Cita::where('id_empleado', $empleadoId)
        ->where('estado', 'concluido')
        ->with(['mascota', 'servicio', 'calificacion'])
        ->orderBy('fecha', 'desc')
        ->get();
    
    $filename = 'mis_servicios_' . date('Y-m-d') . '.csv';
    
    $handle = fopen('php://memory', 'w');
    
    // UTF-8 BOM para acentos
    fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Encabezados
    fputcsv($handle, ['Fecha', 'Mascota', 'Servicio', 'Calificación', 'Comentario']);
    
    // Datos
    foreach ($citas as $cita) {
        fputcsv($handle, [
            $cita->fecha,
            $cita->mascota->nombre ?? 'N/A',
            $cita->servicio->nombre ?? 'N/A',
            $cita->calificacion->puntuacion ?? 'Sin calificar',
            $cita->calificacion->comentario ?? ''
        ]);
    }
    
    rewind($handle);
    $csv = stream_get_contents($handle);
    fclose($handle);
    
    return response($csv, 200)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}

// =========================================================
// MIS ESTADÍSTICAS (Metas personales)
// =========================================================
public function misEstadisticas(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user || $user->rol->nombre !== 'Groomer') {
        return redirect('/');
    }
    
    $empleadoId = $this->getEmpleadoId($user);
    $token = $request->query('token');
    
    // Meta del mes
    $metaMensual = 20;
    $citasEsteMes = Cita::where('id_empleado', $empleadoId)
        ->where('estado', 'concluido')
        ->whereMonth('fecha', now()->month)
        ->whereYear('fecha', now()->year)
        ->count();
    
    // Asegurar que sea un número entero
    $progresoMeta = (int) min(100, round(($citasEsteMes / $metaMensual) * 100));
    
    // Estadísticas por mes
    $estadisticasPorMes = DB::table('citas')
        ->where('id_empleado', $empleadoId)
        ->where('estado', 'concluido')
        ->selectRaw('DATE_TRUNC(\'month\', fecha) as mes, COUNT(*) as total')
        ->groupBy('mes')
        ->orderBy('mes', 'desc')
        ->limit(12)
        ->get();
    
    // Servicios más realizados
    $serviciosTop = DB::table('citas')
        ->where('id_empleado', $empleadoId)
        ->where('estado', 'concluido')
        ->select('id_servicio', DB::raw('COUNT(*) as total'))
        ->groupBy('id_servicio')
        ->orderBy('total', 'desc')
        ->limit(5)
        ->get();
    
    // Cargar nombres de servicios
    foreach ($serviciosTop as $item) {
        $servicio = Servicio::find($item->id_servicio);
        $item->servicio = $servicio;
        $item->nombre = $servicio->nombre ?? 'Desconocido';
    }
    
    return view('personal.groomer.mis-estadisticas', compact(
        'token', 'estadisticasPorMes', 'serviciosTop', 
        'metaMensual', 'citasEsteMes', 'progresoMeta'
    ));
}
// =========================================================
// NOTIFICACIONES (ENVIAR AL CLIENTE)
// =========================================================
private function enviarNotificacionCliente($cita, $tipo, $mensaje)
{
    try {
        $cliente = $cita->mascota->cliente;
        if ($cliente && $cliente->id_usuario) {
            // Crear notificación en la base de datos
            \App\Http\Controllers\NotificacionController::crear(
                $cliente->id_usuario,
                $tipo,
                $mensaje
            );
            
            // Enviar email si está configurado
            if (config('mail.default') !== 'log') {
                try {
                    \Illuminate\Support\Facades\Mail::to($cliente->usuario->correo)
                        ->send(new \App\Mail\NotificacionClienteMail($cita, $tipo, $mensaje));
                } catch (\Exception $e) {
                    Log::error('Error enviando email: ' . $e->getMessage());
                }
            }
        }
    } catch (\Exception $e) {
        Log::error('Error creando notificación: ' . $e->getMessage());
    }
}

}