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
            max-width: 600px; 
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
        h1::before { content: "➕"; font-size: 28px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-weight: 600; font-size: 13px; color: #1e293b; margin-bottom: 5px; }
        label i { margin-right: 6px; color: #4CAF50; width: 18px; }
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
        .back-link { text-align: center; margin-top: 25px; }
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
        .back-link a:hover { color: #4CAF50; background: #e8f5e9; }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .campos-groomer { 
            display: none; 
            border-top: 1px solid #e2e8f0; 
            padding-top: 20px; 
            margin-top: 10px;
        }
        .campos-groomer.visible { display: block; }
        small { display: block; margin-top: 4px; font-size: 11px; color: #64748b; }
        @media (max-width: 600px) {
            .container { padding: 25px; margin: 0 10px; }
            h1 { font-size: 24px; }
            .row-2 { grid-template-columns: 1fr; gap: 0; }
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
            
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nombres *</label>
                <input type="text" name="nombres" value="{{ old('nombres') }}" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-user"></i> Apellidos *</label>
                <input type="text" name="apellidos" value="{{ old('apellidos') }}" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Correo electrónico *</label>
                <input type="email" name="correo" value="{{ old('correo') }}" required>
            </div>
            
            <div class="row-2">
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> Cédula de Identidad</label>
                    <input type="text" name="ci" value="{{ old('ci') }}" placeholder="Opcional">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Teléfono *</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-briefcase"></i> Cargo *</label>
                <select name="cargo" id="cargo" required onchange="mostrarCamposGroomer()">
                    <option value="">Seleccionar cargo</option>
                    <option value="Recepcion" {{ old('cargo') == 'Recepcion' ? 'selected' : '' }}>📞 Recepción</option>
                    <option value="Groomer" {{ old('cargo') == 'Groomer' ? 'selected' : '' }}>✂️ Groomer</option>
                </select>
            </div>
            
            <!-- Campos solo para Groomer -->
            <div id="camposGroomer" class="campos-groomer {{ old('cargo') == 'Groomer' ? 'visible' : '' }}">
                <h3 style="font-size: 16px; margin-bottom: 15px;"><i class="fas fa-cut"></i> Datos de Groomer</h3>
                
                <div class="form-group">
                    <label><i class="fas fa-graduation-cap"></i> Especialidad</label>
                    <input type="text" name="especialidad" value="{{ old('especialidad') }}" placeholder="Ej: Cortes, Baños, Peinados">
                    <small>Opcional</small>
                </div>
                
                <div class="row-2">
                    <div class="form-group">
                        <label><i class="fas fa-users"></i> Capacidad Simultánea</label>
                        <input type="number" name="capacidad_simultanea" value="{{ old('capacidad_simultanea', 1) }}" min="1" max="10">
                        <small>Mascotas que puede atender a la vez</small>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar-day"></i> Capacidad Diaria *</label>
                        <input type="number" name="capacidad_diaria" value="{{ old('capacidad_diaria', 8) }}" min="1" max="20" required>
                        <small>Máximo de citas por día</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Turno</label>
                    <select name="turno">
                        <option value="">Seleccionar turno</option>
                        <option value="Mañana" {{ old('turno') == 'Mañana' ? 'selected' : '' }}>🌅 Mañana (08:00 - 14:00)</option>
                        <option value="Tarde" {{ old('turno') == 'Tarde' ? 'selected' : '' }}>🌇 Tarde (14:00 - 20:00)</option>
                        <option value="Noche" {{ old('turno') == 'Noche' ? 'selected' : '' }}>🌙 Noche (20:00 - 02:00)</option>
                        <option value="Completo" {{ old('turno') == 'Completo' ? 'selected' : '' }}>🔄 Completo (08:00 - 20:00)</option>
                    </select>
                </div>
            </div>
            
            <div class="password-group" style="margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <h3 style="font-size: 16px; margin-bottom: 15px;"><i class="fas fa-lock"></i> Credenciales de Acceso</h3>
                <div class="row-2">
                    <div class="form-group">
                        <label>Contraseña *</label>
                        <input type="password" name="contrasena" required>
                    </div>
                    <div class="form-group">
                        <label>Confirmar Contraseña *</label>
                        <input type="password" name="contrasena_confirmation" required>
                    </div>
                </div>
                <small>Mínimo 8 caracteres. Se enviará por email al empleado.</small>
            </div>
            
            <button type="submit">✅ Crear Empleado</button>
        </form>
        
        <div class="back-link">
            <a href="{{ route('empleados.index') }}?token={{ $token }}">← Volver</a>
        </div>
    </div>

    <script>
        function mostrarCamposGroomer() {
            const cargo = document.getElementById('cargo').value;
            const camposGroomer = document.getElementById('camposGroomer');
            
            if (cargo === 'Groomer') {
                camposGroomer.classList.add('visible');
                // Hacer requeridos los campos de groomer
                document.querySelector('input[name="capacidad_diaria"]').required = true;
            } else {
                camposGroomer.classList.remove('visible');
                // Quitar requeridos
                document.querySelector('input[name="capacidad_diaria"]').required = false;
            }
        }
    </script>
</body>
</html>