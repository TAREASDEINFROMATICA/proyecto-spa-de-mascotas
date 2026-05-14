<!DOCTYPE html>
<html>
<head>
    <title>Empleados - Pet Spa</title>
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
        .btn-add { background: #4CAF50; color: white; padding: 10px 15px; display: inline-block; margin-bottom: 15px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .estado-activo { color: green; font-weight: bold; }
        .estado-inactivo { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👥 Gestión de Empleados</h1>
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        <a href="{{ route('empleados.create') }}" class="btn-add">+ Nuevo Empleado</a>
        
         <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>CI</th>  
                    <th>Teléfono</th>
                    <th>Cargo</th>
                    <th>Especialidad</th>
                    <th>Turno</th>   
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empleados as $empleado)
                <tr>
                    <td>{{ $empleado->id_empleado }}</td>
                    <td>{{ $empleado->usuario->nombres }} {{ $empleado->usuario->apellidos }}</td>
                    <td>{{ $empleado->usuario->correo }}</td>
                    <td>{{ $empleado->usuario->ci ?? '-' }}</td>   <!-- AGREGAR -->

                    <td>{{ $empleado->usuario->telefono }}</td>
                    <td>{{ $empleado->cargo }}</td>
                    <td>{{ $empleado->especialidad ?? '-' }}</td>
                    <td>{{ $empleado->turno ?? '-' }}</td>
                    <td class="{{ $empleado->usuario->estado == 'activo' ? 'estado-activo' : 'estado-inactivo' }}">
                        {{ $empleado->usuario->estado == 'activo' ? '✅ Activo' : '❌ Inactivo' }}
                    </td>
                    <td>
                        <a href="{{ route('empleados.edit', $empleado->id_empleado) }}" class="btn btn-edit">✏️ Editar</a>
                        
                        @if($empleado->usuario->estado == 'activo')
                            <form action="{{ route('empleados.destroy', $empleado->id_empleado) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-delete" onclick="return confirm('¿Desactivar este empleado?')">🗑️ Desactivar</button>
                            </form>
                        @else
                            <form action="{{ route('empleados.activate', $empleado->id_empleado) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-activate" onclick="return confirm('¿Activar este empleado?')">🔄 Activar</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <br>
        <a href="/admin/dashboard">← Volver al Dashboard</a>
    </div>
</body>
</html>