<?php
namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\ProductoVenta;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Pago;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
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

    private function checkAdminOrRecepcion($user)
    {
        return $user && in_array($user->rol->nombre, ['Administrador', 'Recepción']);
    }

    // =========================================================
    // LISTAR VENTAS
    // =========================================================
    public function index(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdminOrRecepcion($user)) {
            return redirect('/');
        }
        
        $ventas = Venta::with(['cliente.usuario', 'detalles.producto'])
            ->orderBy('fecha_venta', 'desc')
            ->paginate(20);
        
        $token = $request->query('token');
        
        return view('admin.ventas.index', compact('ventas', 'token'));
    }

    // =========================================================
    // CREAR VENTA (formulario)
    // =========================================================
public function create(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$this->checkAdminOrRecepcion($user)) {
        return redirect('/');
    }
    
    $clientes = Cliente::with('usuario')->get();
    $productos = ProductoVenta::where('estado', 'activo')
        ->where('stock', '>', 0)
        ->get();
    $metodosPago = MetodoPago::all();
    $token = $request->query('token');
    
    // Verificar si hay datos (para debug)
    if ($metodosPago->count() == 0) {
        // Crear métodos de pago por defecto si no existen
        MetodoPago::create(['nombre' => 'Efectivo', 'descripcion' => 'Pago en efectivo']);
        MetodoPago::create(['nombre' => 'Transferencia', 'descripcion' => 'Transferencia bancaria']);
        MetodoPago::create(['nombre' => 'QR', 'descripcion' => 'Pago con código QR']);
        $metodosPago = MetodoPago::all();
    }
    
    return view('admin.ventas.create', compact('clientes', 'productos', 'metodosPago', 'token'));
}
    // =========================================================
    // GUARDAR VENTA
    // =========================================================
    public function store(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$this->checkAdminOrRecepcion($user)) {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }
    
    $request->validate([
        'id_cliente' => 'required|exists:clientes,id_cliente',
        'productos' => 'required|array',
        'productos.*.id_producto' => 'required|exists:productos_venta,id_producto_venta',
        'productos.*.cantidad' => 'required|integer|min:1',
        'id_metodo_pago' => 'required|exists:metodos_pago,id_metodo_pago',
        'monto_pagado' => 'required|numeric|min:0'
    ]);
    
    DB::beginTransaction();
    
    try {
        $total = 0;
        $detalles = [];
        
        foreach ($request->productos as $item) {
            $producto = ProductoVenta::find($item['id_producto']);
            
            if ($producto->stock < $item['cantidad']) {
                throw new \Exception("Stock insuficiente para {$producto->nombre}");
            }
            
            $subtotal = $producto->precio_venta * $item['cantidad'];
            $total += $subtotal;
            
            $detalles[] = [
                'id_producto_venta' => $item['id_producto'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto->precio_venta,
                'subtotal' => $subtotal
            ];
        }
        
        // Crear venta
        $venta = Venta::create([
            'id_cliente' => $request->id_cliente,
            'fecha_venta' => now(),
            'total' => $total,
            'estado' => 'pagada'
        ]);
        
        // Crear detalles
        foreach ($detalles as $detalle) {
            $detalle['id_venta'] = $venta->id_venta;
            DetalleVenta::create($detalle);
            
            // Descontar stock
            $producto = ProductoVenta::find($detalle['id_producto_venta']);
            $producto->stock -= $detalle['cantidad'];
            $producto->save();
        }
        
        // Crear pago
        Pago::create([
            'id_venta' => $venta->id_venta,
            'id_metodo_pago' => $request->id_metodo_pago,
            'monto' => $request->monto_pagado,
            'fecha_pago' => now(),
            'estado' => 'confirmado'
        ]);
        
        // Crear comprobante
        $numeroComprobante = 'FAC-' . str_pad($venta->id_venta, 8, '0', STR_PAD_LEFT);
        Comprobante::create([
            'id_venta' => $venta->id_venta,
            'tipo_comprobante' => 'FACTURA',
            'numero_comprobante' => $numeroComprobante,
            'fecha_emision' => now()
        ]);
        
        DB::commit();
        
        return response()->json([
            'success' => true, 
            'message' => 'Venta registrada correctamente',
            'venta_id' => $venta->id_venta
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
    }
}

    // =========================================================
    // VER DETALLE DE VENTA
    // =========================================================
    public function show(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdminOrRecepcion($user)) {
            return redirect('/');
        }
        
        $venta = Venta::with(['cliente.usuario', 'detalles.producto', 'pagos.metodoPago', 'comprobante'])
            ->findOrFail($id);
        
        $token = $request->query('token');
        
        return view('admin.ventas.show', compact('venta', 'token'));
    }
    // =========================================================
// BUSCAR PRODUCTOS (para el formulario de venta)
// =========================================================
public function buscarProductos(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$this->checkAdminOrRecepcion($user)) {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }
    
    $search = $request->get('q', '');
    
    $productos = ProductoVenta::where('estado', 'activo')
        ->where('stock', '>', 0)
        ->where(function($query) use ($search) {
            $query->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('id_producto_venta', 'LIKE', "%{$search}%");
        })
        ->limit(10)
        ->get(['id_producto_venta', 'nombre', 'precio_venta', 'stock']);
    
    return response()->json([
        'success' => true,
        'productos' => $productos
    ]);
}
}