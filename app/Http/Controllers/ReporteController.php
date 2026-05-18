<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cita;
use App\Models\ProductoVenta;
use App\Models\InsumoTratamiento;
use App\Models\Pago;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
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
    // REPORTES FINANCIEROS
    // =========================================================
    public function financieros(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $token = $request->query('token');
        
        // Obtener filtros
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->get('fecha_fin', now()->toDateString());
        $tipoReporte = $request->get('tipo', 'diario');
        
        // Ventas por período
        $ventas = Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->orderBy('fecha_venta', 'desc')
            ->get();
        
        $totalVentas = $ventas->sum('total');
        $cantidadVentas = $ventas->count();
        $promedioVenta = $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0;
        
        // Ventas por método de pago
        $ventasPorMetodoPago = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->with('metodoPago')
            ->select('id_metodo_pago', DB::raw('SUM(monto) as total'))
            ->groupBy('id_metodo_pago')
            ->get();
        
        // Productos más vendidos
        $productosTop = DB::table('detalle_ventas')
            ->join('productos_venta', 'detalle_ventas.id_producto_venta', '=', 'productos_venta.id_producto_venta')
            ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
            ->whereBetween('ventas.fecha_venta', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->select('productos_venta.nombre', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'), DB::raw('SUM(detalle_ventas.subtotal) as total_ingresos'))
            ->groupBy('productos_venta.id_producto_venta', 'productos_venta.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();
        
        // Ventas por día (para gráfico)
        $ventasPorDia = Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->select(DB::raw('DATE(fecha_venta) as fecha'), DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as cantidad'))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();
        
        return view('admin.reportes.financieros', compact(
            'token', 'ventas', 'totalVentas', 'cantidadVentas', 'promedioVenta',
            'ventasPorMetodoPago', 'productosTop', 'ventasPorDia',
            'fechaInicio', 'fechaFin', 'tipoReporte'
        ));
    }

    // =========================================================
    // ALERTAS DE STOCK BAJO
    // =========================================================
    public function alertasStock(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $token = $request->query('token');
        
        // Productos con stock bajo
        $productosBajos = ProductoVenta::where('estado', 'activo')
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->orderByRaw('(stock / NULLIF(stock_minimo, 0)) ASC')
            ->get();
        
        // Insumos con stock bajo
        $insumosBajos = InsumoTratamiento::where('estado', 'activo')
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->orderByRaw('(stock / NULLIF(stock_minimo, 0)) ASC')
            ->get();
        
        // Estadísticas de stock
        $totalProductos = ProductoVenta::where('estado', 'activo')->count();
        $totalInsumos = InsumoTratamiento::where('estado', 'activo')->count();
        $productosCriticos = $productosBajos->filter(function($p) { return $p->stock == 0; })->count();
        $insumosCriticos = $insumosBajos->filter(function($i) { return $i->stock == 0; })->count();
        
        return view('admin.reportes.alertas-stock', compact(
            'token', 'productosBajos', 'insumosBajos',
            'totalProductos', 'totalInsumos', 'productosCriticos', 'insumosCriticos'
        ));
    }
}