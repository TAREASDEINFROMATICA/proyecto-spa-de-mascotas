<!DOCTYPE html>
<html>
<head>
    <title>Clientes - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #4CAF50; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; display: inline-block; margin: 2px; }
        .btn-activate { background: #4CAF50; color: white; }
        .btn-deactivate { background: #f44336; color: white; }
        .btn-volver { background: #607d8b; color: white; padding: 10px 15px; display: inline-block; margin-top: 20px; }
        .estado-activo { color: green; font-weight: bold; }
        .estado-inactivo { color: red; font-weight: bold; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐾 Gestión de Clientes</h1>
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>CI</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->id_usuario }}</td>
                    <td>{{ $cliente->ci ?? '-' }}</td>
                    <td>{{ $cliente->nombres }} {{ $cliente->apellidos }}</td>
                    <td>{{ $cliente->correo }}</td>
                    <td>{{ $cliente->telefono ?? '-' }}</td>
                    <td>{{ $cliente->cliente->direccion ?? '-' }}</td>
                    <td class="{{ $cliente->estado == 'activo' ? 'estado-activo' : 'estado-inactivo' }}">
                        {{ $cliente->estado == 'activo' ? '✅ Activo' : '❌ Inactivo' }}
                    </td>
                    <td>{{ $cliente->fecha_registro }}</td>
                    <td>
                        @if($cliente->estado == 'activo')
                            <a href="{{ route('clientes.desactivar', $cliente->id_usuario) }}" 
                               class="btn btn-deactivate" 
                               onclick="return confirm('¿Desactivar este cliente?')">
                                ❌ Desactivar
                            </a>
                        @else
                            <a href="{{ route('clientes.activar', $cliente->id_usuario) }}" 
                               class="btn btn-activate" 
                               onclick="return confirm('¿Activar este cliente?')">
                                ✅ Activar
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <a href="/admin/dashboard" class="btn-volver">← Volver al Dashboard</a>
    </div>
</body>
</html>