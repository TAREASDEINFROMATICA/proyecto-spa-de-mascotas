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
        
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: #1e293b;
            margin-bottom: 5px;
        }
        label i {
            margin-right: 6px;
            color: #2196F3;
            width: 18px;
        }
        input, select { 
            width: 100%; 
            padding: 12px 14px; 
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
        
        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .campos-groomer {
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            margin-top: 10px;
        }
        
        @media (max-width: 600px) {
            .container { padding: 25px; margin: 0 10px; }
            h1 { font-size: 24px; }
            .row-2 { grid-template-columns: 1fr; gap: 0; }
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
            
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nombres *</label>
                <input type="text" name="nombres" value="{{ $empleado->usuario->nombres }}" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-user"></i> Apellidos *</label>
                <input type="text" name="apellidos" value="{{ $empleado->usuario->apellidos }}" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-id-card"></i> Cédula de Identidad</label>
                <input type="text" name="ci" value="{{ $empleado->usuario->ci }}" placeholder="Opcional">
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-phone"></i> Teléfono *</label>
                <input type="text" name="telefono" value="{{ $empleado->usuario->telefono }}" required>
            </div>
            
            <!-- Campos solo para Groomer -->
            @if($empleado->cargo === 'Groomer')
            <div class="campos-groomer">
                <div class="form-group">
                    <label><i class="fas fa-graduation-cap"></i> Especialidad</label>
                    <input type="text" name="especialidad" value="{{ $empleado->especialidad }}" placeholder="Ej: Cortes, Baños, etc.">
                </div>
                
                <div class="row-2">
                    <div class="form-group">
                        <label><i class="fas fa-users"></i> Capacidad Simultánea</label>
                        <input type="number" name="capacidad_simultanea" value="{{ $empleado->capacidad_simultanea ?? 1 }}" min="1" max="10">
                        <small>Mascotas que puede atender a la vez</small>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar-day"></i> Capacidad Diaria *</label>
                        <input type="number" name="capacidad_diaria" value="{{ $empleado->capacidad_diaria ?? 8 }}" min="1" max="20" required>
                        <small>Máximo de citas por día</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Turno</label>
                    <select name="turno">
                        <option value="">Seleccionar turno</option>
                        <option value="Mañana" {{ $empleado->turno == 'Mañana' ? 'selected' : '' }}>🌅 Mañana (08:00 - 14:00)</option>
                        <option value="Tarde" {{ $empleado->turno == 'Tarde' ? 'selected' : '' }}>🌇 Tarde (14:00 - 20:00)</option>
                        <option value="Noche" {{ $empleado->turno == 'Noche' ? 'selected' : '' }}>🌙 Noche (20:00 - 02:00)</option>
                        <option value="Completo" {{ $empleado->turno == 'Completo' ? 'selected' : '' }}>🔄 Completo (08:00 - 20:00)</option>
                    </select>
                </div>
            </div>
            @else
                <!-- Recepción: campos ocultos con valores por defecto -->
                <input type="hidden" name="especialidad" value="{{ $empleado->especialidad }}">
                <input type="hidden" name="capacidad_simultanea" value="{{ $empleado->capacidad_simultanea ?? 1 }}">
                <input type="hidden" name="capacidad_diaria" value="{{ $empleado->capacidad_diaria ?? 8 }}">
                <input type="hidden" name="turno" value="{{ $empleado->turno }}">
            @endif

            <button type="submit">💾 Guardar Cambios</button>
        </form>
        
        <div class="back-link">
            <a href="{{ route('empleados.index') }}?token={{ $token }}">← Volver</a>
        </div>
    </div>
</body>
</html>