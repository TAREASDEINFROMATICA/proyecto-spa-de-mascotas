<?php
namespace App\Http\Controllers;

use App\Models\InsumoTratamiento;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Storage;  // ← IMPORTANTE: Agregar esto

class InsumoController extends Controller
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

    private function checkAdmin($user)
    {
        return $user && $user->rol->nombre === 'Administrador';
    }

    // =========================================================
    // LISTAR INSUMOS
    // =========================================================
    public function index(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $insumos = InsumoTratamiento::orderBy('nombre')->paginate(15);
        $token = $request->query('token');
        
        return view('admin.insumos.index', compact('insumos', 'token'));
    }

    // =========================================================
    // CREAR INSUMO
    // =========================================================
    public function create(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $token = $request->query('token');
        return view('admin.insumos.create', compact('token'));
    }

    public function store(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$this->checkAdmin($user)) {
        return redirect('/');
    }
    
    $request->validate([
        'nombre' => 'required|max:100',
        'unidad_medida' => 'required|max:20',
        'stock' => 'required|numeric|min:0',
        'stock_minimo' => 'required|numeric|min:0',
        'costo_unitario' => 'nullable|numeric|min:0'
    ]);
    
    // Crear el insumo SIN imagen primero
    $insumo = InsumoTratamiento::create([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'stock' => $request->stock,
        'stock_minimo' => $request->stock_minimo,
        'unidad_medida' => $request->unidad_medida,
        'costo_unitario' => $request->costo_unitario,
        'estado' => 'activo',
        'imagen' => null
    ]);
    
    return redirect()->route('admin.insumos.edit', ['id' => $insumo->id_insumo, 'token' => $request->query('token')])
        ->with('success', '✅ Insumo creado correctamente. Ahora puedes agregar una imagen.');
}

// Nuevo método: Guardar imagen después
public function guardarImagen(Request $request, $id)
{
    $user = $this->getUserFromToken($request);
    if (!$this->checkAdmin($user)) {
        return redirect('/');
    }
    
    $insumo = InsumoTratamiento::findOrFail($id);
    
    $request->validate([
        'imagen' => 'required|image|max:2048'
    ]);
    
    // Eliminar imagen vieja si existe
    if ($insumo->imagen && Storage::disk('public')->exists($insumo->imagen)) {
        Storage::disk('public')->delete($insumo->imagen);
    }
    
    $file = $request->file('imagen');
    $nombreImagen = time() . '_' . str_replace(' ', '_', $insumo->nombre) . '.' . $file->getClientOriginalExtension();
    $imagenPath = $file->storeAs('insumos', $nombreImagen, 'public');
    
    $insumo->imagen = $imagenPath;
    $insumo->save();
    
    return redirect()->route('admin.insumos.index', ['token' => $request->query('token')])
        ->with('success', '✅ Imagen agregada correctamente');
}
    // =========================================================
    // EDITAR INSUMO
    // =========================================================
    public function edit(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $insumo = InsumoTratamiento::findOrFail($id);
        $token = $request->query('token');
        
        return view('admin.insumos.edit', compact('insumo', 'token'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $insumo = InsumoTratamiento::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|max:100',
            'unidad_medida' => 'required|max:20',
            'stock_minimo' => 'required|numeric|min:0',
            'costo_unitario' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|image|max:2048'
        ]);
        
        // Actualizar datos básicos
        $insumo->nombre = $request->nombre;
        $insumo->descripcion = $request->descripcion;
        $insumo->stock_minimo = $request->stock_minimo;
        $insumo->unidad_medida = $request->unidad_medida;
        $insumo->costo_unitario = $request->costo_unitario;
        
        // Manejar imagen nueva
        if ($request->hasFile('imagen')) {
            // Eliminar imagen vieja
            if ($insumo->imagen && Storage::disk('public')->exists($insumo->imagen)) {
                Storage::disk('public')->delete($insumo->imagen);
            }
            
            $file = $request->file('imagen');
            $nombreImagen = time() . '_' . str_replace(' ', '_', $request->nombre) . '.' . $file->getClientOriginalExtension();
            $insumo->imagen = $file->storeAs('insumos', $nombreImagen, 'public');
        }
        
        $insumo->save();
        
        return redirect()->route('admin.insumos.index', ['token' => $request->query('token')])
            ->with('success', '✅ Insumo actualizado correctamente');
    }

    // =========================================================
    // AJUSTAR STOCK (ENTRADA/SALIDA MANUAL)
    // =========================================================
    public function ajustarStock(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        $insumo = InsumoTratamiento::findOrFail($id);
        
        $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'nullable|string|max:255'
        ]);
        
        if ($request->tipo === 'entrada') {
            $insumo->stock += $request->cantidad;
            $mensaje = "✅ Se agregaron {$request->cantidad} {$insumo->unidad_medida} al stock";
        } else {
            if ($insumo->stock < $request->cantidad) {
                return response()->json(['success' => false, 'message' => '❌ Stock insuficiente'], 400);
            }
            $insumo->stock -= $request->cantidad;
            $mensaje = "📤 Se retiraron {$request->cantidad} {$insumo->unidad_medida} del stock";
        }
        
        $insumo->save();
        
        return response()->json([
            'success' => true, 
            'message' => $mensaje, 
            'stock' => $insumo->stock,
            'stock_texto' => $insumo->stock . ' ' . $insumo->unidad_medida
        ]);
    }

    // =========================================================
    // CAMBIAR ESTADO (ACTIVAR/DESACTIVAR)
    // =========================================================
    public function toggleEstado(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $insumo = InsumoTratamiento::findOrFail($id);
        $insumo->estado = $insumo->estado === 'activo' ? 'inactivo' : 'activo';
        $insumo->save();
        
        $mensaje = $insumo->estado === 'activo' ? '✅ Insumo activado' : '🔴 Insumo desactivado';
        
        return redirect()->route('admin.insumos.index', ['token' => $request->query('token')])
            ->with('success', $mensaje);
    }
}