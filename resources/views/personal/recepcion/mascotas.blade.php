<!DOCTYPE html>
<html>
<head>
    <title>Mascotas - Recepción</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #e3f2fd; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #2196F3; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; display: inline-block; margin: 2px; }
        .btn-ver { background: #4CAF50; color: white; }
        .btn-volver { background: #607d8b; color: white; padding: 10px 15px; display: inline-block; margin-top: 20px; }
        .estado-activo { color: green; font-weight: bold; }
        .estado-inactivo { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐕 Todas las Mascotas</h1>
        
        @php $token = request()->query('token'); @endphp
        
        <a href="/recepcion/dashboard?token={{ $token }}" class="btn-volver">← Volver al Dashboard</a>
        
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
                    <td><strong>{{ $mascota->nombre }}</strong></td>
                    <td>{{ $mascota->cliente->usuario->nombres }} {{ $mascota->cliente->usuario->apellidos }}</td>
                    <td>{{ $mascota->especie }}</td>
                    <td>{{ $mascota->raza ?? '-' }}</td>
                    <td class="{{ $mascota->estado == 'activa' ? 'estado-activo' : 'estado-inactivo' }}">
                        {{ $mascota->estado == 'activa' ? '✅ Activa' : '❌ Inactiva' }}

                    </td>
                    <td>
                        <a href="/recepcion/mascotas/{{ $mascota->id_mascota }}?token={{ $token }}" class="btn-ver">Ver Detalles</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>