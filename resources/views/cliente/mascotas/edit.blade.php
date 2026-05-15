<!DOCTYPE html>
<html>
<head>
    <title>Editar {{ $mascota->nombre }} - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #fff3e0; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        .error { color: red; margin-bottom: 15px; }
        .foto-actual { text-align: center; margin: 15px 0; }
        .foto-actual img { max-width: 150px; border-radius: 10px; }
        .btn-back { background: #607d8b; text-decoration: none; color: white; padding: 10px 15px; border-radius: 5px; display: inline-block; margin-top: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ Editar {{ $mascota->nombre }}</h2>
        
        @php $token = request()->query('token'); @endphp
        
        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="/cliente/mascotas/{{ $mascota->id_mascota }}?token={{ $token }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="token" value="{{ $token }}">
            
            <!-- Admin y Recepción pueden cambiar el dueño -->
            @if(isset($clientes) && $clientes)
            <select name="id_cliente" required>
                <option value="">Seleccionar dueño</option>
                @foreach($clientes as $c)
                    <option value="{{ $c->id_cliente }}" {{ $mascota->id_cliente == $c->id_cliente ? 'selected' : '' }}>
                        {{ $c->usuario->nombres }} {{ $c->usuario->apellidos }}
                    </option>
                @endforeach
            </select>
            @endif
            
            <input type="text" name="nombre" value="{{ old('nombre', $mascota->nombre) }}" placeholder="Nombre de la mascota *" required>
            
            <select name="especie" required>
                <option value="">Seleccionar especie</option>
                @foreach($especies as $e)
                    <option value="{{ $e }}" {{ $mascota->especie == $e ? 'selected' : '' }}>{{ $e }}</option>
                @endforeach
            </select>
            
            <input type="text" name="raza" value="{{ old('raza', $mascota->raza) }}" placeholder="Raza">
            
            <select name="sexo">
                <option value="">Seleccionar sexo</option>
                <option value="Macho" {{ $mascota->sexo == 'Macho' ? 'selected' : '' }}>Macho</option>
                <option value="Hembra" {{ $mascota->sexo == 'Hembra' ? 'selected' : '' }}>Hembra</option>
            </select>
            
            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $mascota->fecha_nacimiento) }}">
            <input type="number" step="0.1" name="peso" value="{{ old('peso', $mascota->peso) }}" placeholder="Peso (kg)">
            <input type="text" name="color" value="{{ old('color', $mascota->color) }}" placeholder="Color">
            
            <select name="temperamento_general">
                <option value="">Seleccionar temperamento</option>
                @foreach($temperamentos as $t)
                    <option value="{{ $t }}" {{ $mascota->temperamento_general == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
            
            <textarea name="alergias" placeholder="Alergias">{{ old('alergias', $mascota->alergias) }}</textarea>
            <textarea name="cuidados_especiales" placeholder="Cuidados especiales">{{ old('cuidados_especiales', $mascota->cuidados_especiales) }}</textarea>
            <textarea name="observaciones" placeholder="Observaciones">{{ old('observaciones', $mascota->observaciones) }}</textarea>
            
            @if($mascota->foto)
                <div class="foto-actual">
                    <label>Foto actual:</label><br>
                    <img src="{{ Storage::url($mascota->foto) }}" alt="{{ $mascota->nombre }}">
                </div>
            @endif
            
            <input type="file" name="foto" accept="image/*">
            
            <button type="submit">💾 Guardar Cambios</button>
        </form>
        
        <!-- Botón volver según el rol -->
        @if($rol == 'admin')
            <a href="/admin/mascotas?token={{ $token }}" class="btn-back">← Volver a Mascotas</a>
        @elseif($rol == 'recepcion')
            <a href="/recepcion/mascotas?token={{ $token }}" class="btn-back">← Volver a Mascotas</a>
        @else
            <a href="/cliente/mascotas/{{ $mascota->id_mascota }}?token={{ $token }}" class="btn-back">← Volver a Ficha</a>
        @endif
    </div>
</body>
</html>