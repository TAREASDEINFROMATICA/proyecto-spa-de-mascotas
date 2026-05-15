<!DOCTYPE html>
<html>
<head>
    <title>Servicios - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #4CAF50; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; display: inline-block; margin: 2px; }
        .btn-add { background: #4CAF50; color: white; padding: 10px 15px; margin-bottom: 15px; display: inline-block; }
        .btn-edit { background: #2196F3; color: white; }
        .btn-delete { background: #f44336; color: white; }
        .btn-activate { background: #4CAF50; color: white; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .estado-activo { color: green; font-weight: bold; }
        .estado-inactivo { color: red; font-weight: bold; }
        .precio { font-weight: bold; color: #2196F3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✂️ Servicios del Spa</h1>
        
        @php
            $token = request()->query('token');
        @endphp
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        
        <a href="/admin/servicios/create?token={{ $token }}" class="btn-add">+ Nuevo Servicio</a>
        <a href="/admin/dashboard?token={{ $token }}">← Volver al Dashboard</a>
        
        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Duración</th>
                    <th>Precio</th>
                    <th>Tipo Mascota</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicios as $servicio)
                <tr>
                    <td>{{ $servicio->id_servicio }}</td>
                    <td><strong>{{ $servicio->nombre }}</strong></td>
                    <td>{{ $servicio->descripcion ?? '-' }}</td>
                    <td>{{ $servicio->duracion_minutos }} min</td>
                    <td class="precio">${{ number_format($servicio->precio, 2) }}</td>
                    <td>{{ $servicio->tipo_mascota ?? 'Todos' }}</td>
                    <td class="{{ $servicio->estado == 'activo' ? 'estado-activo' : 'estado-inactivo' }}">
                        {{ $servicio->estado == 'activo' ? '✅ Activo' : '❌ Inactivo' }}
                    </td>
                    <td>
                        <a href="/admin/servicios/{{ $servicio->id_servicio }}/edit?token={{ $token }}" class="btn btn-edit">✏️ Editar</a>
                        @if($servicio->estado == 'activo')
                            <a href="/admin/servicios/{{ $servicio->id_servicio }}/desactivate?token={{ $token }}" class="btn btn-delete" onclick="return confirm('¿Desactivar este servicio?')">❌ Desactivar</a>
                        @else
                            <a href="/admin/servicios/{{ $servicio->id_servicio }}/activate?token={{ $token }}" class="btn btn-activate">🔄 Activar</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>