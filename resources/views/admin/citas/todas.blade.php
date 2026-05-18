<!DOCTYPE html>
<html>
<head>
    <title>Todas las Citas - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1400px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .btn-primary { background: white; color: #2196F3; }
        .content { padding: 30px; }
        
        /* Tarjetas de estadísticas */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-left: 4px solid; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .value { font-size: 28px; font-weight: 700; }
        .stat-card .label { color: #666; font-size: 14px; margin-top: 5px; }
        
        /* Filtros */
        .filtros { display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; align-items: flex-end; }
        .filtro-group { display: flex; flex-direction: column; gap: 5px; }
        .filtro-group label { font-size: 12px; color: #666; font-weight: 600; }
        .filtro-group input, .filtro-group select { padding: 10px; border: 1px solid #ddd; border-radius: 8px; min-width: 150px; }
        .btn-filtrar { background: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .btn-filtrar:hover { background: #45a049; }
        
        /* Tabla */
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; font-size: 14px; }
        td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; font-size: 14px; }
        tr:hover { background: #f9f9f9; }
        
        .estado-reservado { background: #fff3e0; color: #ff9800; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .estado-programado { background: #e8f5e9; color: #4CAF50; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .estado-concluido { background: #e3f2fd; color: #2196F3; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .estado-cancelado { background: #ffebee; color: #f44336; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        
        .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; }
        
        @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .filtros { flex-direction: column; } table { font-size: 12px; } th, td { padding: 8px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calendar-alt"></i> Gestión de Citas</h1>
            <a href="/admin/dashboard?token={{ $token }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
        
        <div class="content">
            <!-- Tarjetas de estadísticas -->
            <div class="stats-grid">
                <div class="stat-card" style="border-left-color: #2196F3;">
                    <div class="value">{{ $totalCitas }}</div>
                    <div class="label">Total citas</div>
                </div>
                <div class="stat-card" style="border-left-color: #4CAF50;">
                    <div class="value">{{ $citasHoy }}</div>
                    <div class="label">Citas hoy</div>
                </div>
                <div class="stat-card" style="border-left-color: #ff9800;">
                    <div class="value">{{ $citasPendientes }}</div>
                    <div class="label">Pendientes</div>
                </div>
                <div class="stat-card" style="border-left-color: #4CAF50;">
                    <div class="value">{{ $citasProgramadas }}</div>
                    <div class="label">Confirmadas</div>
                </div>
                <div class="stat-card" style="border-left-color: #2196F3;">
                    <div class="value">{{ $citasConcluidas }}</div>
                    <div class="label">Concluidas</div>
                </div>
                <div class="stat-card" style="border-left-color: #f44336;">
                    <div class="value">{{ $citasCanceladas }}</div>
                    <div class="label">Canceladas</div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="filtros">
                <div class="filtro-group">
                    <label>Fecha desde</label>
                    <input type="date" id="fecha_desde">
                </div>
                <div class="filtro-group">
                    <label>Fecha hasta</label>
                    <input type="date" id="fecha_hasta">
                </div>
                <div class="filtro-group">
                    <label>Estado</label>
                    <select id="estado">
                        <option value="">Todos</option>
                        <option value="reservado">Pendiente</option>
                        <option value="programado">Confirmada</option>
                        <option value="concluido">Concluida</option>
                        <option value="cancelado">Cancelada</option>
                    </select>
                </div>
                <div class="filtro-group">
                    <label>Mascota</label>
                    <input type="text" id="mascota" placeholder="Nombre mascota">
                </div>
                <button class="btn-filtrar" onclick="aplicarFiltros()"><i class="fas fa-search"></i> Filtrar</button>
                <button class="btn-filtrar" onclick="limpiarFiltros()" style="background: #607d8b;"><i class="fas fa-eraser"></i> Limpiar</button>
            </div>
            
            <!-- Tabla de citas -->
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Mascota</th>
                            <th>Servicio</th>
                            <th>Groomer</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCitas">
                        @foreach($citas as $cita)
                        <tr data-id="{{ $cita->id_cita }}" data-fecha="{{ $cita->fecha }}" data-estado="{{ $cita->estado }}" data-mascota="{{ strtolower($cita->mascota->nombre) }}">
                            <td>{{ $cita->id_cita }}</td>
                            <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($cita->hora_fin)->format('H:i') }}</td>
                            <td>{{ $cita->mascota->nombre }} <small>({{ $cita->mascota->especie }})</small></td>
                            <td>{{ $cita->servicio->nombre }}</td>
                            <td>{{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}</td>
                            <td>
                                <span class="estado-{{ $cita->estado }}">
                                    @if($cita->estado == 'reservado') ⏳ Pendiente
                                    @elseif($cita->estado == 'programado') ✅ Confirmada
                                    @elseif($cita->estado == 'concluido') 🏁 Concluida
                                    @else ❌ Cancelada
                                    @endif
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                {{ $citas->appends(['token' => $token])->links() }}
            </div>
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
        
        function aplicarFiltros() {
            const fechaDesde = document.getElementById('fecha_desde').value;
            const fechaHasta = document.getElementById('fecha_hasta').value;
            const estado = document.getElementById('estado').value;
            const mascota = document.getElementById('mascota').value.toLowerCase();
            
            const rows = document.querySelectorAll('#tablaCitas tr');
            
            rows.forEach(row => {
                let mostrar = true;
                
                // Filtro por fecha
                if (fechaDesde) {
                    const fechaFila = row.getAttribute('data-fecha');
                    if (fechaFila < fechaDesde) mostrar = false;
                }
                if (fechaHasta && mostrar) {
                    const fechaFila = row.getAttribute('data-fecha');
                    if (fechaFila > fechaHasta) mostrar = false;
                }
                
                // Filtro por estado
                if (estado && mostrar) {
                    const estadoFila = row.getAttribute('data-estado');
                    if (estadoFila !== estado) mostrar = false;
                }
                
                // Filtro por mascota
                if (mascota && mostrar) {
                    const mascotaFila = row.getAttribute('data-mascota');
                    if (!mascotaFila.includes(mascota)) mostrar = false;
                }
                
                row.style.display = mostrar ? '' : 'none';
            });
        }
        
        function limpiarFiltros() {
            document.getElementById('fecha_desde').value = '';
            document.getElementById('fecha_hasta').value = '';
            document.getElementById('estado').value = '';
            document.getElementById('mascota').value = '';
            
            const rows = document.querySelectorAll('#tablaCitas tr');
            rows.forEach(row => {
                row.style.display = '';
            });
        }
    </script>
</body>
</html>