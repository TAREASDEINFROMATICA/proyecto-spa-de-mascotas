<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cita;
use App\Models\ProductoVenta;
use App\Models\InsumoTratamiento;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AdminController extends Controller
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

    public function dashboard(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$user || $user->rol->nombre !== 'Administrador') {
            return redirect('/');
        }
        
        $token = $request->query('token');
        
        // =========================================================
        // ESTADÍSTICAS DE VENTAS
        // =========================================================
        $ventasHoy = Venta::whereDate('fecha_venta', today())->sum('total');
        $ventasMes = Venta::whereMonth('fecha_venta', now()->month)
            ->whereYear('fecha_venta', now()->year)
            ->sum('total');
        $ventasTotales = Venta::sum('total');
        
        // =========================================================
        // ESTADÍSTICAS DE CITAS
        // =========================================================
        $citasHoy = Cita::whereDate('fecha', today())->count();
        $citasPendientes = Cita::where('estado', 'reservado')->count();
        $citasConfirmadas = Cita::where('estado', 'programado')->count();
        $citasConcluidas = Cita::where('estado', 'concluido')->count();
        $citasCanceladas = Cita::where('estado', 'cancelado')->count();
        
        // =========================================================
        // ALERTAS DE STOCK BAJO
        // =========================================================
        $productosStockBajo = ProductoVenta::where('estado', 'activo')
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->count();
        
        $insumosStockBajo = InsumoTratamiento::where('estado', 'activo')
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->count();
        
        // =========================================================
        // VENTAS POR DÍA (ÚLTIMOS 7 DÍAS)
        // =========================================================
       // Ventas por día (últimos 7 días)
$ventasPorDia = [];
for ($i = 6; $i >= 0; $i--) {
    $fecha = now()->subDays($i);
    $total = Venta::whereDate('fecha_venta', $fecha)->sum('total');
    $ventasPorDia[] = [
        'fecha' => $fecha->format('d/m'),
        'total' => $total
    ];
}

// Citas por estado (para el gráfico)
$citasPorEstado = [
    'Pendientes' => $citasPendientes,
    'Confirmadas' => $citasConfirmadas,
    'Concluidas' => $citasConcluidas,
    'Canceladas' => $citasCanceladas
];
        
        // =========================================================
        // CITAS POR ESTADO (PARA GRÁFICO)
        // =========================================================
     
        
        return view('admin.dashboard', compact(
            'token',
            'ventasHoy', 'ventasMes', 'ventasTotales',
            'citasHoy', 'citasPendientes', 'citasConfirmadas',
            'citasConcluidas', 'citasCanceladas',
            'productosStockBajo', 'insumosStockBajo',
            'ventasPorDia', 'citasPorEstado'
        ));
    }
}