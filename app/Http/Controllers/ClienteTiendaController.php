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
        
        // Obtener carrito de la sesión o cookie
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
            return redirect('/');
        }
        
        $request->validate([
            'id_producto' => 'required|exists:productos_venta,id_producto_venta',
            'cantidad' => 'required|integer|min:1'
        ]);
        
        $producto = ProductoVenta::find($request->id_producto);
        
        if ($producto->stock < $request->cantidad) {
            return response()->json(['success' => false, 'message' => 'Stock insuficiente'], 400);
        }
        
        $carrito = $this->getCarrito();
        
        if (isset($carrito[$request->id_producto])) {
            $carrito[$request->id_producto]['cantidad'] += $request->cantidad;
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
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
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
            return redirect('/');
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
            return redirect()->route('cliente.catalogo', ['token' => $request->query('token')])
                ->with('error', 'El carrito está vacío');
        }
        
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        $metodosPago = MetodoPago::all();
        $token = $request->query('token');
        
        return view('cliente.tienda.checkout', compact('carrito', 'total', 'metodosPago', 'token'));
    }

    // =========================================================
    // PROCESAR PAGO Y CREAR VENTA
    // =========================================================
    public function procesarPago(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Cliente') {
            return redirect('/');
        }
        
        $carrito = $this->getCarrito();
        
        if (empty($carrito)) {
            return response()->json(['success' => false, 'message' => 'Carrito vacío'], 400);
        }
        
        $request->validate([
            'id_metodo_pago' => 'required|exists:metodos_pago,id_metodo_pago',
        ]);
        
        DB::beginTransaction();
        
        try {
            $total = 0;
            $detalles = [];
            
            // Verificar stock y calcular total
            foreach ($carrito as $item) {
                $producto = ProductoVenta::find($item['id']);
                
                if (!$producto || $producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$item['nombre']}");
                }
                
                $subtotal = $item['precio'] * $item['cantidad'];
                $total += $subtotal;
                
                $detalles[] = [
                    'id_producto_venta' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $subtotal
                ];
            }
            
            // Crear venta
            $venta = Venta::create([
                'id_cliente' => $user->cliente->id_cliente,
                'fecha_venta' => now(),
                'total' => $total,
                'estado' => 'pagada'
            ]);
            
            // Crear detalles y descontar stock
            foreach ($detalles as $detalle) {
                $detalle['id_venta'] = $venta->id_venta;
                DetalleVenta::create($detalle);
                
                $producto = ProductoVenta::find($detalle['id_producto_venta']);
                $producto->stock -= $detalle['cantidad'];
                $producto->save();
            }
            
            // Crear pago
            Pago::create([
                'id_venta' => $venta->id_venta,
                'id_metodo_pago' => $request->id_metodo_pago,
                'monto' => $total,
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
            
            // Vaciar carrito
            $this->vaciarCarrito();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Compra realizada con éxito',
                'venta_id' => $venta->id_venta
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
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