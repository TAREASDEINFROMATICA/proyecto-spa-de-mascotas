<!DOCTYPE html>
<html>
<head>
    <title>Citas Pendientes - Recepción</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); color: white; padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; border: none; cursor: pointer; font-size: 14px; }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }
        .btn-confirmar { background: #4CAF50; color: white; padding: 6px 12px; border-radius: 8px; font-size: 12px; }
        .btn-confirmar:hover { background: #45a049; }
        .btn-rechazar { background: #f44336; color: white; padding: 6px 12px; border-radius: 8px; font-size: 12px; }
        .btn-rechazar:hover { background: #d32f2f; }
        .content { padding: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; font-size: 14px; }
        td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; font-size: 14px; }
        tr:hover { background: #f9f9f9; }
        .estado-reservado { background: #fff3e0; color: #ff9800; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .empty-message { text-align: center; padding: 60px; color: #999; }
        .empty-message i { font-size: 48px; margin-bottom: 15px; display: block; }
        .acciones { display: flex; gap: 8px; flex-wrap: wrap; }
        @media (max-width: 768px) { .header { flex-direction: column; text-align: center; } table { font-size: 12px; } th, td { padding: 8px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-clock"></i> Citas Pendientes de Confirmación</h1>
            @if($rol == 'admin')
                <a href="/admin/dashboard?token={{ $token }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            @else
                <a href="/recepcion/dashboard?token={{ $token }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            @endif
        </div>
        
        <div class="content">
            @if($citas->count() > 0)
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Mascota</th>
                                <th>Servicio</th>
                                <th>Groomer</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($citas as $cita)
                            <tr>
                                <td>{{ $cita->id_cita }}</td>
                                <td>{{ $cita->mascota->cliente->usuario->nombres }} {{ $cita->mascota->cliente->usuario->apellidos }}</td>
                                <td>{{ $cita->mascota->nombre }} <small>({{ $cita->mascota->especie }})</small></td>
                                <td>{{ $cita->servicio->nombre }}</td>
                                <td>{{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}</td>
                                <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($cita->hora_fin)->format('H:i') }}</td>
                                <td><span class="estado-reservado"><i class="fas fa-hourglass-half"></i> Reservado</span></td>
                                <td>
                                    <div class="acciones">
                                        <button onclick="confirmarCita('{{ $cita->id_cita }}')" class="btn-confirmar">
                                            <i class="fas fa-check-circle"></i> Confirmar
                                        </button>
                                        <button onclick="rechazarCita('{{ $cita->id_cita }}')" class="btn-rechazar">
                                            <i class="fas fa-times-circle"></i> Rechazar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-message">
                    <i class="fas fa-check-circle"></i>
                    <h3>No hay citas pendientes</h3>
                    <p>Todas las citas han sido confirmadas o canceladas.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
        
        function confirmarCita(citaId) {
            if (!confirm('¿Confirmar esta cita?')) {
                return;
            }
            
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch('/admin/citas/' + citaId + '/confirmar?token=' + token, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar';
                }
            })
            .catch(error => {
                alert('❌ Error de conexión');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar';
            });
        }
        
        function rechazarCita(citaId) {
            if (!confirm('¿Cancelar esta cita? El cliente será notificado.')) {
                return;
            }
            
            const motivo = prompt('Motivo de cancelación:');
            if (!motivo) return;
            
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch('/admin/citas/' + citaId + '/cancel?token=' + token, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ motivo: motivo, observaciones: '' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-times-circle"></i> Rechazar';
                }
            })
            .catch(error => {
                alert('❌ Error de conexión');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-times-circle"></i> Rechazar';
            });
        }
    </script>
</body>
</html>