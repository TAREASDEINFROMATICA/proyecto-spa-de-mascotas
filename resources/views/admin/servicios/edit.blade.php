<!DOCTYPE html>
<html>
<head>
    <title>Editar Servicio - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #f5f5f5; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #2196F3; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ Editar Servicio: {{ $servicio->nombre }}</h2>
        
        @php
            $token = request()->query('token');
        @endphp
        
        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="/admin/servicios/{{ $servicio->id_servicio }}?token={{ $token }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="token" value="{{ $token }}">
            
            <input type="text" name="nombre" value="{{ old('nombre', $servicio->nombre) }}" placeholder="Nombre del servicio *" required>
            <textarea name="descripcion" placeholder="Descripción" rows="3">{{ old('descripcion', $servicio->descripcion) }}</textarea>
            
            <label>Duración (minutos) *</label>
            <input type="number" name="duracion_minutos" value="{{ old('duracion_minutos', $servicio->duracion_minutos) }}" min="5" max="480" required>
            
            <label>Precio *</label>
            <input type="number" step="0.01" name="precio" value="{{ old('precio', $servicio->precio) }}" min="0" required>
            
            <select name="tipo_mascota">
                <option value="">Todos los tipos de mascota</option>
                <option value="Perro" {{ $servicio->tipo_mascota == 'Perro' ? 'selected' : '' }}>Perro</option>
                <option value="Gato" {{ $servicio->tipo_mascota == 'Gato' ? 'selected' : '' }}>Gato</option>
                <option value="Ambos" {{ $servicio->tipo_mascota == 'Ambos' ? 'selected' : '' }}>Ambos</option>
                <option value="Otro" {{ $servicio->tipo_mascota == 'Otro' ? 'selected' : '' }}>Otro</option>
            </select>
            
            <button type="submit">💾 Guardar Cambios</button>
        </form>
        
        <br>
        <a href="/admin/servicios?token={{ $token }}">← Volver</a>
    </div>
</body>
</html>