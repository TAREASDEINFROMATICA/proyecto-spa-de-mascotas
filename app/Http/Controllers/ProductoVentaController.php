<?php
namespace App\Http\Controllers;

use App\Models\ProductoVenta;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Storage;  // ← IMPORTANTE: Agregar esto

class ProductoVentaController extends Controller
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
    // LISTAR PRODUCTOS
    // =========================================================
    public function index(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $productos = ProductoVenta::with('categoria')->orderBy('nombre')->paginate(15);
        $token = $request->query('token');
        
        return view('admin.productos.index', compact('productos', 'token'));
    }

    // =========================================================
    // CREAR PRODUCTO
    // =========================================================
    public function create(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $categorias = CategoriaProducto::all();  // ← CORREGIDO: eliminar where('estado')
        $token = $request->query('token');
        
        return view('admin.productos.create', compact('categorias', 'token'));
    }

    // =========================================================
    // GUARDAR PRODUCTO (CON IMAGEN)
    // =========================================================
    public function store(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$this->checkAdmin($user)) {
        return redirect('/');
    }
    
    $request->validate([
        'id_categoria' => 'required|exists:categorias_producto,id_categoria',
        'nombre' => 'required|max:100',
        'precio_venta' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'stock_minimo' => 'required|integer|min:0',
        'unidad_medida' => 'required|max:20'
    ]);
    
    // Crear el producto SIN imagen primero
    $producto = ProductoVenta::create([
        'id_categoria' => $request->id_categoria,
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'precio_compra' => $request->precio_compra,
        'precio_venta' => $request->precio_venta,
        'stock' => $request->stock,
        'stock_minimo' => $request->stock_minimo,
        'unidad_medida' => $request->unidad_medida,
        'estado' => 'activo',
        'imagen' => null
    ]);
    
    return redirect()->route('admin.productos.edit', ['id' => $producto->id_producto_venta, 'token' => $request->query('token')])
        ->with('success', '✅ Producto creado correctamente. Ahora puedes agregar una imagen.');
}

// Nuevo método: Guardar imagen después
public function guardarImagen(Request $request, $id)
{
    $user = $this->getUserFromToken($request);
    if (!$this->checkAdmin($user)) {
        return redirect('/');
    }
    
    $producto = ProductoVenta::findOrFail($id);
    
    $request->validate([
        'imagen' => 'required|image|max:2048'
    ]);
    
    // Eliminar imagen vieja si existe
    if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
        Storage::disk('public')->delete($producto->imagen);
    }
    
    $file = $request->file('imagen');
    $nombreImagen = time() . '_' . str_replace(' ', '_', $producto->nombre) . '.' . $file->getClientOriginalExtension();
    $imagenPath = $file->storeAs('productos', $nombreImagen, 'public');
    
    $producto->imagen = $imagenPath;
    $producto->save();
    
    return redirect()->route('admin.productos.index', ['token' => $request->query('token')])
        ->with('success', '✅ Imagen agregada correctamente');
}

    // =========================================================
    // EDITAR PRODUCTO
    // =========================================================
    public function edit(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $producto = ProductoVenta::findOrFail($id);
        $categorias = CategoriaProducto::all();  // ← CORREGIDO
        $token = $request->query('token');
        
        return view('admin.productos.edit', compact('producto', 'categorias', 'token'));
    }

    // =========================================================
    // ACTUALIZAR PRODUCTO (CON IMAGEN)
    // =========================================================
    public function update(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $producto = ProductoVenta::findOrFail($id);
        
        $request->validate([
            'id_categoria' => 'required|exists:categorias_producto,id_categoria',
            'nombre' => 'required|max:100',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'unidad_medida' => 'required|max:20',
            'imagen' => 'nullable|image|max:2048'
        ]);
        
        // Manejar imagen nueva
        if ($request->hasFile('imagen')) {
            // Eliminar imagen vieja
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            
            $file = $request->file('imagen');
            $nombreImagen = time() . '_' . str_replace(' ', '_', $request->nombre) . '.' . $file->getClientOriginalExtension();
            $producto->imagen = $file->storeAs('productos', $nombreImagen, 'public');
        }
        
        $producto->update([
            'id_categoria' => $request->id_categoria,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio_compra' => $request->precio_compra,
            'precio_venta' => $request->precio_venta,
            'stock_minimo' => $request->stock_minimo,
            'unidad_medida' => $request->unidad_medida
        ]);
        
        return redirect()->route('admin.productos.index', ['token' => $request->query('token')])
            ->with('success', '✅ Producto actualizado correctamente');
    }

    // =========================================================
    // AJUSTAR STOCK
    // =========================================================
    public function ajustarStock(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        $producto = ProductoVenta::findOrFail($id);
        
        $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string'
        ]);
        
        if ($request->tipo === 'entrada') {
            $producto->stock += $request->cantidad;
            $mensaje = "✅ Se agregaron {$request->cantidad} {$producto->unidad_medida} al stock";
        } else {
            if ($producto->stock < $request->cantidad) {
                return response()->json(['success' => false, 'message' => '❌ Stock insuficiente'], 400);
            }
            $producto->stock -= $request->cantidad;
            $mensaje = "📤 Se retiraron {$request->cantidad} {$producto->unidad_medida} del stock";
        }
        
        $producto->save();
        
        return response()->json(['success' => true, 'message' => $mensaje, 'stock' => $producto->stock]);
    }

    // =========================================================
    // CAMBIAR ESTADO
    // =========================================================
    public function toggleEstado(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $producto = ProductoVenta::findOrFail($id);
        $producto->estado = $producto->estado === 'activo' ? 'inactivo' : 'activo';
        $producto->save();
        
        $mensaje = $producto->estado === 'activo' ? '✅ Producto activado' : '🔴 Producto desactivado';
        
        return redirect()->route('admin.productos.index', ['token' => $request->query('token')])
            ->with('success', $mensaje);
    }
}