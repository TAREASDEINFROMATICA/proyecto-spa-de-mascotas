<!DOCTYPE html>
<html>
<head>
    <title>Editar Empleado - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 50px 20px; 
        }
        .container { 
            max-width: 550px; 
            margin: auto; 
            background: white; 
            padding: 35px; 
            border-radius: 24px; 
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); 
        }
        
        h1 { 
            font-size: 28px; 
            font-weight: 700; 
            color: #1e293b; 
            margin-bottom: 25px; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        h1::before { content: "✏️"; font-size: 28px; }
        
        input, select { 
            width: 100%; 
            padding: 12px 14px; 
            margin: 8px 0; 
            border: 2px solid #e2e8f0; 
            border-radius: 12px; 
            font-size: 14px; 
            transition: all 0.3s; 
            font-family: 'Inter', sans-serif;
        }
        input:focus, select:focus { 
            outline: none; 
            border-color: #2196F3; 
            box-shadow: 0 0 0 3px rgba(33,150,243,0.1); 
        }
        
        button { 
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); 
            color: white; 
            padding: 14px; 
            border: none; 
            border-radius: 14px; 
            cursor: pointer; 
            font-weight: 600; 
            font-size: 16px; 
            width: 100%; 
            transition: all 0.3s; 
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        button:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 20px rgba(33,150,243,0.3); 
        }
        
        .error { 
            color: #c62828; 
            background: #ffebee; 
            padding: 14px; 
            border-radius: 12px; 
            margin-bottom: 20px; 
            border-left: 4px solid #f44336;
            font-size: 14px;
        }
        
        .back-link { 
            text-align: center; 
            margin-top: 25px; 
        }
        .back-link a { 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            color: #607d8b; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 500;
            transition: all 0.3s;
            padding: 8px 16px;
            border-radius: 10px;
        }
        .back-link a:hover { 
            color: #2196F3; 
            background: #e3f2fd;
        }
        
        input::placeholder { color: #94a3b8; }
        select { background: white; cursor: pointer; }
        
        @media (max-width: 600px) {
            .container { padding: 25px; margin: 0 10px; }
            h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Empleado</h1>
        
        @php $token = request()->query('token'); @endphp

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('empleados.update', $empleado->id_empleado) }}?token={{ $token }}">
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
        
        <div class="back-link">
            <a href="{{ route('empleados.index') }}?token={{ $token }}">← Volver</a>
        </div>
    </div>
</body>
</html>