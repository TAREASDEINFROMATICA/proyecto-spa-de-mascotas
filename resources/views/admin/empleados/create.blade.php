<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Empleado - Pet Spa</title>
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
            margin-bottom: 8px; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        h1::before { content: "➕"; font-size: 28px; }
        
        h3 { 
            font-size: 16px; 
            font-weight: 600; 
            color: #334155; 
            margin: 20px 0 15px 0; 
            padding-bottom: 8px; 
            border-bottom: 2px solid #e2e8f0; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
        }
        h3:first-of-type { margin-top: 0; }
        
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
            border-color: #4CAF50; 
            box-shadow: 0 0 0 3px rgba(76,175,80,0.1); 
        }
        
        button { 
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); 
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
            box-shadow: 0 8px 20px rgba(76,175,80,0.3); 
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
        
        .password-group { 
            margin-top: 20px; 
            border-top: 1px solid #e2e8f0; 
            padding-top: 20px; 
        }
        .password-group label { font-weight: 600; color: #1e293b; }
        small { 
            color: #64748b; 
            display: block; 
            margin-top: -5px; 
            margin-bottom: 10px; 
            font-size: 11px; 
        }
        .required { color: #f44336; }
        
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
            color: #4CAF50; 
            background: #f0fdf4;
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
        <h1>Nuevo Empleado</h1>
        
        @php $token = request()->query('token'); @endphp

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('empleados.store') }}?token={{ $token }}">
            @csrf
            
            <h3>📋 Datos Personales</h3>
            
            <input type="text" name="nombres" placeholder="Nombres *" 
                   pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+" 
                   title="Solo letras y espacios" 
                   value="{{ old('nombres') }}" required>
            <small>Solo letras y espacios</small>
            
            <input type="text" name="apellidos" placeholder="Apellidos *" 
                   pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+" 
                   title="Solo letras y espacios" 
                   value="{{ old('apellidos') }}" required>
            <small>Solo letras y espacios</small>
            
            <input type="email" name="correo" placeholder="Correo electrónico *" 
                   value="{{ old('correo') }}" required>
            
            <input type="text" name="ci" placeholder="Cédula de Identidad" 
                   pattern="[0-9]{6,12}" 
                   title="Solo números, 6 a 12 dígitos" 
                   value="{{ old('ci') }}">
            <small>Solo números, 6 a 12 dígitos (opcional)</small>
            
            <input type="tel" name="telefono" placeholder="Teléfono *" 
                   pattern="[0-9]{8,15}" 
                   title="Solo números, 8 a 15 dígitos" 
                   value="{{ old('telefono') }}" required>
            <small>Solo números, 8 a 15 dígitos</small>
            
            <h3>💼 Datos Laborales</h3>
            
            <select name="cargo" required>
                <option value="">Seleccionar cargo</option>
                <option value="Recepcion" {{ old('cargo') == 'Recepcion' ? 'selected' : '' }}>📞 Recepción</option>
                <option value="Groomer" {{ old('cargo') == 'Groomer' ? 'selected' : '' }}>✂️ Groomer</option>
            </select>

            <select name="turno">
                <option value="">Seleccionar turno</option>
                <option value="Mañana" {{ old('turno') == 'Mañana' ? 'selected' : '' }}>🌅 Mañana (08:00 - 14:00)</option>
                <option value="Tarde" {{ old('turno') == 'Tarde' ? 'selected' : '' }}>🌇 Tarde (14:00 - 20:00)</option>
                <option value="Noche" {{ old('turno') == 'Noche' ? 'selected' : '' }}>🌙 Noche (20:00 - 02:00)</option>
                <option value="Completo" {{ old('turno') == 'Completo' ? 'selected' : '' }}>🔄 Completo (08:00 - 20:00)</option>
            </select>
               
            <input type="text" name="especialidad" placeholder="Especialidad (solo para Groomer)" 
                   pattern="[A-Za-z0-9\s]+" 
                   title="Solo letras, números y espacios" 
                   value="{{ old('especialidad') }}">
            <small>Solo letras, números y espacios</small>
            
            <input type="number" name="capacidad_simultanea" placeholder="Capacidad simultánea (1-10)" 
                   value="{{ old('capacidad_simultanea', 1) }}" min="1" max="10">
            
            <!-- CAMPOS DE CONTRASEÑA -->
            <div class="password-group">
                <h3>🔒 Credenciales de Acceso</h3>
                <input type="password" name="contrasena" placeholder="Contraseña *" required>
                <input type="password" name="contrasena_confirmation" placeholder="Confirmar Contraseña *" required>
                <small>Mínimo 8 caracteres. Se enviará por email al empleado.</small>
            </div>
            
            <button type="submit">✅ Crear Empleado</button>
        </form>
        
        <div class="back-link">
            <a href="{{ route('empleados.index') }}?token={{ $token }}">← Volver</a>
        </div>
    </div>
</body>
</html>