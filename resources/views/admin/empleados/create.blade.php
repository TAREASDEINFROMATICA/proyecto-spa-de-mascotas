<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Empleado - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #f5f5f5; }
        .container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .password-group { margin-top: 15px; border-top: 1px solid #ddd; padding-top: 15px; }
        .password-group label { font-weight: bold; }
        small { color: #666; display: block; margin-top: -5px; margin-bottom: 10px; }
        .required { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Nuevo Empleado</h1>
        
        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('empleados.store') }}">
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
        
        <br>
        <a href="{{ route('empleados.index') }}">← Volver</a>
    </div>
</body>
</html>