<!DOCTYPE html>
<html>
<head>
    <title>Citas Pendientes - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #FF9800; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; display: inline-block; margin: 2px; }
        .btn-confirm { background: #4CAF50; color: white; }
        .btn-reject { background: #f44336; color: white; }
        .pendiente { color: orange; font-weight: bold; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⏳ Citas Pendientes de Confirmación</h1>
        
        @php $token = request()->query('token'); 
                $rol = $rol ?? 'admin'; // Valor por defecto si no se pasó desde el controlador
        @endphp
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        
       @if($rol == 'admin')
    <a href="/admin/dashboard?token={{ $token }}" class="btn-volver">← Volver al Dashboard de Admin</a>
@else
    <a href="/recepcion/dashboard?token={{ $token }}" class="btn-volver">← Volver al Dashboard de Recepción</a>
@endif
        <table style="margin-top: 20px;">
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
                    <td>{{ $cita->mascota->nombre }}</td>
                    <td>{{ $cita->servicio->nombre }}</td>
                    <td>{{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}</td>
                    <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</td>
                    <td class="pendiente">⏳ {{ ucfirst($cita->estado) }}</td>
                    <td>
                        <form method="POST" action="/admin/citas/{{ $cita->id_cita }}/confirmar?token={{ $token }}" style="display: inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-confirm" onclick="return confirm('¿Confirmar esta cita?')">✅ Confirmar</button>
                        </form>
                        <form method="POST" action="/admin/citas/{{ $cita->id_cita }}/cancel?token={{ $token }}" style="display: inline-block;">
                            @csrf
                            <input type="hidden" name="motivo" value="Rechazada por recepción">
                            <button type="submit" class="btn btn-reject" onclick="return confirm('¿Rechazar esta cita?')">❌ Rechazar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>