<!DOCTYPE html>
<html>
<head>
    <title>Todas las Mascotas - Admin</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #4CAF50; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; display: inline-block; margin: 2px; }
        .btn-edit { background: #2196F3; color: white; }
        .btn-delete { background: #f44336; color: white; }
        .btn-activate { background: #4CAF50; color: white; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .estado-activo { color: green; font-weight: bold; }
        .estado-inactivo { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐕 Todas las Mascotas</h1>
        
        @php
            $token = request()->query('token');
        @endphp
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        
        <a href="/admin/dashboard?token={{ $token }}">← Volver al Dashboard</a>
        
        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dueño</th>
                    <th>Especie</th>
                    <th>Raza</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mascotas as $mascota)
                <tr>
                    <td>{{ $mascota->id_mascota }}</td>
                    <td>{{ $mascota->nombre }}</td>
                    <td>{{ $mascota->cliente->usuario->nombres }} {{ $mascota->cliente->usuario->apellidos }}</td>
                    <td>{{ $mascota->especie }}</td>
                    <td>{{ $mascota->raza ?? '-' }}</td>
                    <td class="{{ $mascota->estado == 'activa' ? 'estado-activo' : 'estado-inactivo' }}">
                        {{ $mascota->estado == 'activa' ? '✅ Activa' : '❌ Inactiva' }}
                    </td>
                    <td>
                        <a href="/cliente/mascotas/{{ $mascota->id_mascota }}?token={{ $token }}" class="btn btn-edit">👁️ Ver</a>
                        @if($mascota->estado == 'activa')
                            <a href="/admin/mascotas/{{ $mascota->id_mascota }}/activate?token={{ $token }}" class="btn btn-activate" onclick="return confirm('¿Desactivar esta mascota?')">❌ Desactivar</a>
                        @else
                            <a href="/admin/mascotas/{{ $mascota->id_mascota }}/activate?token={{ $token }}" class="btn btn-activate">🔄 Activar</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>