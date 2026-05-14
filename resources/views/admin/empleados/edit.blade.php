<!DOCTYPE html>
<html>
<head>
    <title>Editar Empleado - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #f5f5f5; }
        .container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #2196F3; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Editar Empleado</h1>
        
        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('empleados.update', $empleado->id_empleado) }}">
            @csrf @method('PUT')
            <input type="text" name="nombres" value="{{ $empleado->usuario->nombres }}" required>
            <input type="text" name="apellidos" value="{{ $empleado->usuario->apellidos }}" required>
            <input type="text" name="ci" value="{{ $empleado->usuario->ci }}" placeholder="Cédula de Identidad">
            <input type="text" name="telefono" value="{{ $empleado->usuario->telefono }}" required>
            <input type="text" name="especialidad" value="{{ $empleado->especialidad }}" placeholder="Especialidad">
            <input type="number" name="capacidad_simultanea" value="{{ $empleado->capacidad_simultanea }}" min="1" max="10">
            <select name="turno">
                <option value="">Seleccionar turno</option>
                <option value="Mañana" {{ $empleado->turno == 'Mañana' ? 'selected' : '' }}>🌅 Mañana (08:00 - 14:00)</option>
                <option value="Tarde" {{ $empleado->turno == 'Tarde' ? 'selected' : '' }}>🌇 Tarde (14:00 - 20:00)</option>
                <option value="Noche" {{ $empleado->turno == 'Noche' ? 'selected' : '' }}>🌙 Noche (20:00 - 02:00)</option>
                <option value="Completo" {{ $empleado->turno == 'Completo' ? 'selected' : '' }}>🔄 Completo (08:00 - 20:00)</option>
            </select>

            <button type="submit">💾 Guardar Cambios</button>
        </form>
        
        <br>
        <a href="{{ route('empleados.index') }}">← Volver</a>
    </div>
</body>
</html>