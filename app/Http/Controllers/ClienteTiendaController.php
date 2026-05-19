<?php
namespace App\Http\Controllers;

use App\Models\ProductoVenta;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pago;
use App\Models\MetodoPago;
use App\Models\Comprobante;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Log;


class ClienteTiendaController extends Controller
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

    // =========================================================
    // CATÁLOGO DE PRODUCTOS
    // =========================================================
    public function catalogo(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Cliente') {
            return redirect('/');
        }
        
        $productos = ProductoVenta::where('estado', 'activo')
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->paginate(12);
        
        $carrito = $this->getCarrito();
        $token = $request->query('token');
        
        return view('cliente.tienda.catalogo', compact('productos', 'carrito', 'token'));
    }

    // =========================================================
    // AGREGAR AL CARRITO
    // =========================================================
    public function agregarCarrito(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Cliente') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }

        $request->validate([
            'id_producto' => 'required|exists:productos_venta,id_producto_venta',
            'cantidad' => 'required|integer|min:1'
        ]);

        $producto = ProductoVenta::find($request->id_producto);

        if ($producto->estado !== 'activo') {
            return response()->json(['success' => false, 'message' => 'Producto no disponible'], 400);
        }

        if ($producto->stock < $request->cantidad) {
            return response()->json(['success' => false, 'message' => "Stock insuficiente. Solo hay {$producto->stock} unidades"], 400);
        }

        $carrito = $this->getCarrito();

        if (isset($carrito[$request->id_producto])) {
            $nuevaCantidad = $carrito[$request->id_producto]['cantidad'] + $request->cantidad;
            if ($nuevaCantidad > $producto->stock) {
                return response()->json(['success' => false, 'message' => "Stock insuficiente. Solo puedes agregar {$producto->stock} unidades en total"], 400);
            }
            $carrito[$request->id_producto]['cantidad'] = $nuevaCantidad;
        } else {
            $carrito[$request->id_producto] = [
                'id' => $producto->id_producto_venta,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio_venta,
                'cantidad' => $request->cantidad,
                'stock' => $producto->stock,
                'imagen' => $producto->imagen
            ];
        }

        $this->guardarCarrito($carrito);
        $totalItems = array_sum(array_column($carrito, 'cantidad'));

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'total_items' => $totalItems,
            'carrito' => $carrito
        ]);
    }

    // =========================================================
    // VER CARRITO
    // =========================================================
    public function verCarrito(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Cliente') {
            return redirect('/');
        }

        $carrito = $this->getCarrito();
        $total = 0;

        foreach ($carrito as $key => $item) {
            $producto = ProductoVenta::find($item['id']);
            if (!$producto || $producto->estado !== 'activo') {
                unset($carrito[$key]);
            } else {
                $carrito[$key]['precio'] = $producto->precio_venta;
                $carrito[$key]['stock'] = $producto->stock;
                $total += $producto->precio_venta * $item['cantidad'];
            }
        }

        $this->guardarCarrito($carrito);
        $token = $request->query('token');

        return view('cliente.tienda.carrito', compact('carrito', 'total', 'token'));
    }

    // =========================================================
    // ACTUALIZAR CANTIDAD EN CARRITO
    // =========================================================
    public function actualizarCarrito(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Cliente') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        
        $request->validate([
            'id_producto' => 'required',
            'cantidad' => 'required|integer|min:0'
        ]);
        
        $carrito = $this->getCarrito();
        
        if ($request->cantidad <= 0) {
            unset($carrito[$request->id_producto]);
        } else {
            if (isset($carrito[$request->id_producto])) {
                $carrito[$request->id_producto]['cantidad'] = $request->cantidad;
            }
        }
        
        $this->guardarCarrito($carrito);
        
        return response()->json(['success' => true]);
    }

    // =========================================================
    // ELIMINAR DEL CARRITO
    // =========================================================
    public function eliminarCarrito(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Cliente') {
            return redirect('/');
        }
        
        $carrito = $this->getCarrito();
        unset($carrito[$id]);
        $this->guardarCarrito($carrito);
        
        return redirect()->route('cliente.carrito', ['token' => $request->query('token')])
            ->with('success', 'Producto eliminado del carrito');
    }

    // =========================================================
    // CHECKOUT - FINALIZAR COMPRA
    // =========================================================
    public function checkout(Request $request)
{
    $user = $this->getUserFromToken($request);
    if (!$user || $user->rol->nombre !== 'Cliente') {
        return redirect('/');
    }
    
    $carrito = $this->getCarrito();
    
    if (empty($carrito)) {
        return redirect()->route('cliente.carrito', ['token' => $request->query('token')])
            ->with('error', 'El carrito está vacío');
    }
    
    $total = 0;
    foreach ($carrito as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }
    
    // ✅ IMPORTANTE: Obtener métodos de pago
    $metodosPago = \App\Models\MetodoPago::all();
    $token = $request->query('token');
    
    return view('cliente.tienda.checkout', compact('carrito', 'total', 'metodosPago', 'token'));
}

    // =========================================================
    // PROCESAR PAGO Y CREAR VENTA
    // =========================================================
   public function procesarPago(Request $request)
{
    Log::info('=== PROCESAR PAGO INICIADO ===');
    Log::info('Request data: ' . json_encode($request->all()));
    
    $user = $this->getUserFromToken($request);
    if (!$user || $user->rol->nombre !== 'Cliente') {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }

    // Si viene productos en el request, usar esos. Si no, usar el carrito de sesión
    if ($request->has('productos') && !empty($request->productos)) {
        $productos = $request->productos;
        Log::info('Usando productos del request: ' . json_encode($productos));
    } else {
        $carrito = $this->getCarrito();
        $productos = [];
        foreach ($carrito as $item) {
            $productos[] = [
                'id_producto' => $item['id'],
                'cantidad' => $item['cantidad']
            ];
        }
        Log::info('Usando carrito de sesión: ' . json_encode($productos));
    }

    if (empty($productos)) {
        return response()->json(['success' => false, 'message' => 'Carrito vacío'], 400);
    }

    $request->validate([
        'id_metodo_pago' => 'required|numeric|min:1|max:4',
    ]);

    DB::beginTransaction();

    try {
        $total = 0;
        $detalles = [];

        foreach ($productos as $item) {
            $producto = ProductoVenta::find($item['id_producto']);

            if (!$producto) {
                throw new \Exception("Producto no encontrado");
            }

            if ($producto->stock < $item['cantidad']) {
                throw new \Exception("Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock}");
            }

            $subtotal = $producto->precio_venta * $item['cantidad'];
            $total += $subtotal;

            $detalles[] = [
                'id_producto_venta' => $producto->id_producto_venta,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto->precio_venta,
                'subtotal' => $subtotal
            ];
        }

        $venta = Venta::create([
            'id_cliente' => $user->cliente->id_cliente,
            'fecha_venta' => now(),
            'total' => $total,
            'estado' => 'pagada'
        ]);

        foreach ($detalles as $detalle) {
            $detalle['id_venta'] = $venta->id_venta;
            DetalleVenta::create($detalle);

            $producto = ProductoVenta::find($detalle['id_producto_venta']);
            $producto->stock -= $detalle['cantidad'];
            $producto->save();
        }

        Pago::create([
            'id_venta' => $venta->id_venta,
            'id_metodo_pago' => $request->id_metodo_pago,
            'monto' => $total,
            'fecha_pago' => now(),
            'estado' => 'confirmado'
        ]);

        $numeroComprobante = 'FAC-' . str_pad($venta->id_venta, 8, '0', STR_PAD_LEFT);
        Comprobante::create([
            'id_venta' => $venta->id_venta,
            'tipo_comprobante' => 'FACTURA',
            'numero_comprobante' => $numeroComprobante,
            'fecha_emision' => now()
        ]);

        $this->vaciarCarrito();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Compra realizada con éxito',
            'venta_id' => $venta->id_venta
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error en procesarPago: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
    }
}
    // =========================================================
    // MIS COMPRAS (HISTORIAL)
    // =========================================================
    public function misCompras(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Cliente') {
            return redirect('/');
        }
        
        $ventas = Venta::where('id_cliente', $user->cliente->id_cliente)
            ->with(['detalles.producto', 'pagos.metodoPago'])
            ->orderBy('fecha_venta', 'desc')
            ->paginate(10);
        
        $token = $request->query('token');
        
        return view('cliente.tienda.mis-compras', compact('ventas', 'token'));
    }

    // =========================================================
    // DETALLE DE COMPRA
    // =========================================================
    public function detalleCompra(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Cliente') {
            return redirect('/');
        }
        
        $venta = Venta::where('id_cliente', $user->cliente->id_cliente)
            ->with(['detalles.producto', 'pagos.metodoPago', 'comprobante'])
            ->findOrFail($id);
        
        $token = $request->query('token');
        
        return view('cliente.tienda.detalle-compra', compact('venta', 'token'));
    }

    // =========================================================
    // FUNCIONES PRIVADAS PARA MANEJAR CARRITO
    // =========================================================
    private function getCarrito()
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito) && isset($_COOKIE['carrito'])) {
            $carrito = json_decode($_COOKIE['carrito'], true) ?: [];
        }
        return $carrito;
    }
    
    private function guardarCarrito($carrito)
    {
        session()->put('carrito', $carrito);
        setcookie('carrito', json_encode($carrito), time() + (86400 * 7), "/");
    }
    
    private function vaciarCarrito()
    {
        session()->forget('carrito');
        setcookie('carrito', '', time() - 3600, "/");
    }
}