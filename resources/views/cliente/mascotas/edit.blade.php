<!DOCTYPE html>
<html>
<head>
    <title>Editar {{ $mascota->nombre }} - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #fff3e0; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; margin-bottom: 15px; }
        .foto-actual { text-align: center; margin: 15px 0; }
        .foto-actual img { max-width: 150px; border-radius: 10px; }
        .btn-back { background: #607d8b; text-decoration: none; color: white; padding: 10px 15px; border-radius: 5px; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ Editar {{ $mascota->nombre }}</h2>
        
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
        
        <form method="POST" action="/cliente/mascotas/{{ $mascota->id_mascota }}?token={{ $token }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="token" value="{{ $token }}">
            
            <input type="text" name="nombre" value="{{ old('nombre', $mascota->nombre) }}" placeholder="Nombre de la mascota *" required>
            
            <select name="especie" required>
                <option value="">Seleccionar especie</option>
                <option value="Perro" {{ $mascota->especie == 'Perro' ? 'selected' : '' }}>Perro</option>
                <option value="Gato" {{ $mascota->especie == 'Gato' ? 'selected' : '' }}>Gato</option>
                <option value="Conejo" {{ $mascota->especie == 'Conejo' ? 'selected' : '' }}>Conejo</option>
                <option value="Hamster" {{ $mascota->especie == 'Hamster' ? 'selected' : '' }}>Hamster</option>
                <option value="Ave" {{ $mascota->especie == 'Ave' ? 'selected' : '' }}>Ave</option>
                <option value="Otro" {{ $mascota->especie == 'Otro' ? 'selected' : '' }}>Otro</option>
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
                <option value="tranquilo" {{ $mascota->temperamento_general == 'tranquilo' ? 'selected' : '' }}>Tranquilo</option>
                <option value="nervioso" {{ $mascota->temperamento_general == 'nervioso' ? 'selected' : '' }}>Nervioso</option>
                <option value="agresivo" {{ $mascota->temperamento_general == 'agresivo' ? 'selected' : '' }}>Agresivo</option>
                <option value="miedoso" {{ $mascota->temperamento_general == 'miedoso' ? 'selected' : '' }}>Miedoso</option>
                <option value="jugueton" {{ $mascota->temperamento_general == 'jugueton' ? 'selected' : '' }}>Jugueton</option>
                <option value="otro" {{ $mascota->temperamento_general == 'otro' ? 'selected' : '' }}>Otro</option>
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
        
        <a href="/cliente/mascotas?token={{ $token }}" class="btn-back">← Volver</a>
    </div>
</body>
</html>