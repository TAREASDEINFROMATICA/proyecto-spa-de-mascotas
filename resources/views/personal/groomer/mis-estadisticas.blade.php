<!DOCTYPE html>
<html>
<head>
    <title>Mis Estadísticas - Groomer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .content { padding: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); color: white; padding: 20px; border-radius: 16px; text-align: center; }
        .stat-card .numero { font-size: 32px; font-weight: 700; }
        .meta-progress { background: #f5f5f5; border-radius: 12px; padding: 20px; margin-bottom: 30px; }
        .progress-bar { background: #e0e0e0; border-radius: 10px; height: 20px; overflow: hidden; margin: 10px 0; }
        .progress-fill { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); height: 100%; border-radius: 10px; transition: width 0.5s ease; width: 0%; }
        .servicio-item { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #eee; }
        .btn { background: #607d8b; color: white; padding: 10px 20px; border: none; border-radius: 12px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-csv { background: #f44336; }
        .btn-csv:hover { background: #d32f2f; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Mis Estadísticas</h1>
            <p>Rendimiento y productividad</p>
        </div>
        
        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="numero">{{ $citasEsteMes ?? 0 }}</div>
                    <div>Citas este mes</div>
                </div>
                <div class="stat-card">
                    <div class="numero">{{ $progresoMeta ?? 0 }}%</div>
                    <div>Meta mensual</div>
                </div>
            </div>
            
            <div class="meta-progress">
                <h3><i class="fas fa-bullseye"></i> Meta del mes: {{ $metaMensual ?? 20 }} citas</h3>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <div>Llevas {{ $citasEsteMes ?? 0 }} de {{ $metaMensual ?? 20 }} citas</div>
            </div>
            
            <h3><i class="fas fa-chart-bar"></i> Servicios más realizados</h3>
            @if(isset($serviciosTop) && count($serviciosTop) > 0)
                @foreach($serviciosTop as $servicio)
                <div class="servicio-item">
                    <span>{{ $servicio->servicio->nombre ?? $servicio->nombre ?? 'Servicio' }}</span>
                    <span><strong>{{ $servicio->total }} veces</strong></span>
                </div>
                @endforeach
            @else
                <div class="servicio-item">
                    <span>No hay servicios registrados</span>
                    <span><strong>0 veces</strong></span>
                </div>
            @endif
            
            <div style="margin-top: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="/groomer/exportar-csv?token={{ $token }}" class="btn btn-csv" target="_blank">
                    <i class="fas fa-file-csv"></i> Exportar a CSV
                </a>
                <a href="/groomer/dashboard?token={{ $token }}" class="btn">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        // Forzar la barra de progreso con JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            var progreso = '{{ (int)($progresoMeta ?? 0) }}';
            var fill = document.getElementById('progressFill');
            if (fill) {
                fill.style.width = progreso + '%';
            }
        });
    </script>
</body>
</html>