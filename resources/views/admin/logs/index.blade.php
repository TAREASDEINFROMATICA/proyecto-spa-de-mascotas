<!DOCTYPE html>
<html>
<head>
    <title>Logs del Sistema - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #4CAF50; color: white; }
        .filtros { margin-bottom: 20px; padding: 15px; background: #f0f0f0; border-radius: 5px; }
        .filtros input, .filtros select { padding: 5px; margin-right: 10px; }
        .btn { background: #2196F3; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
        .btn-volver { background: #607d8b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Logs de Auditoría</h1>
        
        <div class="filtros">
            <form method="GET">
                <input type="text" name="buscar" placeholder="Buscar acción..." value="{{ request('buscar') }}">
                <select name="usuario">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id_usuario }}" {{ request('usuario') == $u->id_usuario ? 'selected' : '' }}>
                            {{ $u->nombres }} {{ $u->apellidos }} ({{ $u->rol->nombre }})
                        </option>
                    @endforeach
                </select>
                <button type="submit">Filtrar</button>
                <a href="{{ route('logs.index') }}" class="btn">Limpiar</a>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acción</th>
                    <th>IP</th>
                    <th>Navegador</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->id_log }}</td>
                    <td>{{ $log->usuario ? $log->usuario->nombres . ' ' . $log->usuario->apellidos : 'No autenticado' }}</td>
                    <td>{{ $log->usuario ? $log->usuario->rol->nombre : '-' }}</td>
                    <td>{{ $log->accion }}</td>
                    <td>{{ $log->ip_address ?? '-' }}</td>
                    <td title="{{ $log->user_agent }}">{{ $log->user_agent ? substr($log->user_agent, 0, 50) . '...' : '-' }}</td>
                    <td>{{ $log->fecha }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            {{ $logs->links() }}
        </div>
        
        <br>
        <a href="/admin/dashboard" class="btn btn-volver">← Volver al Dashboard</a>
    </div>
</body>
</html>