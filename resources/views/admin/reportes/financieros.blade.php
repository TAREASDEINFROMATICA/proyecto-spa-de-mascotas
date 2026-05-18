<!DOCTYPE html>
<html>
<head>
    <title>Reportes Financieros - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1400px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .btn-primary { background: white; color: #4CAF50; }
        .content { padding: 30px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-left: 4px solid #4CAF50; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .value { font-size: 28px; font-weight: 700; color: #4CAF50; }
        .stat-card .label { color: #666; font-size: 14px; margin-top: 5px; }
        
        .filtros { background: #f8f9fa; padding: 20px; border-radius: 16px; margin-bottom: 30px; display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end; }
        .filtro-group { display: flex; flex-direction: column; gap: 5px; }
        .filtro-group label { font-size: 12px; color: #666; font-weight: 600; }
        .filtro-group input, .filtro-group select { padding: 10px; border: 1px solid #ddd; border-radius: 8px; min-width: 150px; }
        .btn-filtrar { background: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; }
        
        .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin: 30px 0; }
        .chart-card { background: #f8f9fa; border-radius: 16px; padding: 20px; }
        .chart-card h3 { margin-bottom: 15px; color: #333; }
        canvas { max-height: 300px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        
        @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .charts-grid { grid-template-columns: 1fr; } .filtros { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Reportes Financieros</h1>
            <a href="/admin/dashboard?token={{ $token }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
        
        <div class="content">
            <!-- Filtros -->
            <form class="filtros" method="GET" action="{{ route('admin.reportes.financieros', ['token' => $token]) }}">
                <div class="filtro-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}">
                </div>
                <div class="filtro-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ $fechaFin }}">
                </div>
                <div class="filtro-group">
                    <label>Tipo de Reporte</label>
                    <select name="tipo">
                        <option value="diario" {{ $tipoReporte == 'diario' ? 'selected' : '' }}>Diario</option>
                        <option value="semanal" {{ $tipoReporte == 'semanal' ? 'selected' : '' }}>Semanal</option>
                        <option value="mensual" {{ $tipoReporte == 'mensual' ? 'selected' : '' }}>Mensual</option>
                    </select>
                </div>
                <button type="submit" class="btn-filtrar"><i class="fas fa-search"></i> Aplicar Filtros</button>
            </form>
            
            <!-- Tarjetas de resumen -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="value">Bs {{ number_format($totalVentas, 2) }}</div>
                    <div class="label">Total Ventas</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ $cantidadVentas }}</div>
                    <div class="label">N° de Ventas</div>
                </div>
                <div class="stat-card">
                    <div class="value">Bs {{ number_format($promedioVenta, 2) }}</div>
                    <div class="label">Ticket Promedio</div>
                </div>
            </div>
            
            <!-- Gráficos -->
            <div class="charts-grid">
                <div class="chart-card">
                    <h3><i class="fas fa-chart-bar"></i> Ventas por Día</h3>
                    <canvas id="ventasChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3><i class="fas fa-chart-pie"></i> Ventas por Método de Pago</h3>
                    <canvas id="metodosChart"></canvas>
                </div>
            </div>
            
            <!-- Productos más vendidos -->
            <h3><i class="fas fa-trophy"></i> Productos más vendidos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad Vendida</th>
                        <th>Total Ingresos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productosTop as $producto)
                    <tr>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->total_vendido }} unid.</td>
                        <td>Bs {{ number_format($producto->total_ingresos, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Lista de ventas -->
            <h3 style="margin-top: 30px;"><i class="fas fa-list"></i> Detalle de Ventas</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $venta)
                    <tr>
                        <td>#{{ $venta->id_venta }}</td>
                        <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}</td>
                        <td>Bs {{ number_format($venta->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

   <script>
    const token = '{{ $token }}';
    
    // =========================================================
    // GRÁFICOS - TEMPORALMENTE COMENTADOS HASTA QUE EL CONTROLADOR FUNCIONE
    // =========================================================
    
    /*
    // Gráfico de ventas por día
    const ventasData = {!! json_encode($ventasPorDia) !!};
    if (ventasData && ventasData.length > 0) {
        const ventasChart = new Chart(document.getElementById('ventasChart'), {
            type: 'bar',
            data: {
                labels: ventasData.map(v => v.fecha),
                datasets: [{
                    label: 'Ventas (Bs)',
                    data: ventasData.map(v => v.total),
                    backgroundColor: '#4CAF50',
                    borderRadius: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
    } else {
        document.getElementById('ventasChart').innerHTML = '<div style="text-align: center; padding: 50px;">No hay datos de ventas en este período</div>';
    }
    
    // Gráfico de métodos de pago
    const metodosData = {!! json_encode($ventasPorMetodoPago) !!};
    if (metodosData && metodosData.length > 0) {
        const metodosChart = new Chart(document.getElementById('metodosChart'), {
            type: 'doughnut',
            data: {
                labels: metodosData.map(m => m.metodo_pago?.nombre || 'No definido'),
                datasets: [{
                    data: metodosData.map(m => m.total),
                    backgroundColor: ['#4CAF50', '#2196F3', '#ff9800', '#9C27B0', '#f44336']
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
    } else {
        document.getElementById('metodosChart').innerHTML = '<div style="text-align: center; padding: 50px;">No hay datos de métodos de pago</div>';
    }
    */
    
    // Mostrar mensaje temporal
    document.getElementById('ventasChart').innerHTML = '<div style="text-align: center; padding: 50px;">Gráficos disponibles cuando haya datos de ventas</div>';
    document.getElementById('metodosChart').innerHTML = '<div style="text-align: center; padding: 50px;">Gráficos disponibles cuando haya datos de ventas</div>';
</script>
</body>
</html>