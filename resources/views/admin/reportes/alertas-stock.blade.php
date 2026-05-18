<!DOCTYPE html>
<html>
<head>
    <title>Alertas de Stock - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .content { padding: 30px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-left: 4px solid; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .value { font-size: 28px; font-weight: 700; }
        .stat-card .label { color: #666; font-size: 14px; margin-top: 5px; }
        
        .alerta-card { background: white; border-radius: 16px; padding: 15px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-left: 4px solid #f44336; }
        .alerta-card.critico { border-left-color: #f44336; background: #ffebee; }
        .alerta-card.bajo { border-left-color: #ff9800; background: #fff3e0; }
        .alerta-info { flex: 1; }
        .alerta-nombre { font-weight: 600; font-size: 16px; }
        .alerta-stock { font-size: 12px; color: #666; margin-top: 5px; }
        .alerta-stock span { font-weight: 700; }
        .badge-critico { background: #f44336; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-bajo { background: #ff9800; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-normal { background: #4CAF50; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        
        h2 { font-size: 20px; margin: 30px 0 15px 0; display: flex; align-items: center; gap: 10px; padding-bottom: 8px; border-bottom: 2px solid #e0e0e0; }
        h2 i { color: #ff9800; }
        
        @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .header { flex-direction: column; text-align: center; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-exclamation-triangle"></i> Alertas de Stock Bajo</h1>
            <a href="/admin/dashboard?token={{ $token }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Dashboard</a>
        </div>
        
        <div class="content">
            <!-- Tarjetas de resumen -->
            <div class="stats-grid">
                <div class="stat-card" style="border-left-color: #2196F3;">
                    <div class="value">{{ $totalProductos ?? 0 }}</div>
                    <div class="label">Total Productos</div>
                </div>
                <div class="stat-card" style="border-left-color: #4CAF50;">
                    <div class="value">{{ $totalInsumos ?? 0 }}</div>
                    <div class="label">Total Insumos</div>
                </div>
                <div class="stat-card" style="border-left-color: #f44336;">
                    <div class="value">{{ $productosCriticos ?? 0 }}</div>
                    <div class="label">Stock Crítico (Productos)</div>
                </div>
                <div class="stat-card" style="border-left-color: #ff9800;">
                    <div class="value">{{ $insumosCriticos ?? 0 }}</div>
                    <div class="label">Stock Crítico (Insumos)</div>
                </div>
            </div>
            
            <!-- Productos con stock bajo -->
            <h2><i class="fas fa-box"></i> Productos con Stock Bajo</h2>
            @if(isset($productosBajos) && $productosBajos->count() > 0)
                @foreach($productosBajos as $producto)
                <div class="alerta-card {{ $producto->stock == 0 ? 'critico' : 'bajo' }}">
                    <div class="alerta-info">
                        <div class="alerta-nombre">{{ $producto->nombre }}</div>
                        <div class="alerta-stock">
                            Stock actual: <span>{{ $producto->stock }} {{ $producto->unidad_medida }}</span> | 
                            Stock mínimo: {{ $producto->stock_minimo }} {{ $producto->unidad_medida }}
                        </div>
                    </div>
                    <div>
                        @if($producto->stock == 0)
                            <span class="badge-critico"><i class="fas fa-times-circle"></i> AGOTADO</span>
                        @else
                            <span class="badge-bajo"><i class="fas fa-exclamation-triangle"></i> Stock Bajo</span>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 40px; color: #4CAF50;">
                    <i class="fas fa-check-circle" style="font-size: 48px;"></i>
                    <p style="margin-top: 10px;">No hay productos con stock bajo</p>
                </div>
            @endif
            
            <!-- Insumos con stock bajo -->
            <h2><i class="fas fa-flask"></i> Insumos con Stock Bajo</h2>
            @if(isset($insumosBajos) && $insumosBajos->count() > 0)
                @foreach($insumosBajos as $insumo)
                <div class="alerta-card {{ $insumo->stock == 0 ? 'critico' : 'bajo' }}">
                    <div class="alerta-info">
                        <div class="alerta-nombre">{{ $insumo->nombre }}</div>
                        <div class="alerta-stock">
                            Stock actual: <span>{{ $insumo->stock }} {{ $insumo->unidad_medida }}</span> | 
                            Stock mínimo: {{ $insumo->stock_minimo }} {{ $insumo->unidad_medida }}
                        </div>
                    </div>
                    <div>
                        @if($insumo->stock == 0)
                            <span class="badge-critico"><i class="fas fa-times-circle"></i> AGOTADO</span>
                        @else
                            <span class="badge-bajo"><i class="fas fa-exclamation-triangle"></i> Stock Bajo</span>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 40px; color: #4CAF50;">
                    <i class="fas fa-check-circle" style="font-size: 48px;"></i>
                    <p style="margin-top: 10px;">No hay insumos con stock bajo</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>