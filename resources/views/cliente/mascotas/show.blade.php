<!DOCTYPE html>
<html>
<head>
    <title>{{ $mascota->nombre }} - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #fff3e0; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        .foto { text-align: center; margin-bottom: 20px; }
        .foto img { max-width: 300px; border-radius: 10px; }
        .info { margin: 20px 0; }
        .info label { font-weight: bold; display: inline-block; width: 150px; }
        .btn { background: #4CAF50; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn-back { background: #607d8b; }
        .btn-edit { background: #2196F3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐕 {{ $mascota->nombre }}</h1>
        
        @php
            $token = request()->query('token');
        @endphp
        
        <div class="foto">
            @if($mascota->foto)
                <img src="{{ Storage::url($mascota->foto) }}" alt="{{ $mascota->nombre }}">
            @else
                <div style="background: #eee; padding: 50px; text-align: center;">🐾 Sin foto</div>
            @endif
        </div>
        
        <div class="info">
            <label>Dueño:</label> {{ $mascota->cliente->usuario->nombres }} {{ $mascota->cliente->usuario->apellidos }}<br>
            <label>Especie:</label> {{ $mascota->especie }}<br>
            <label>Raza:</label> {{ $mascota->raza ?? '-' }}<br>
            <label>Sexo:</label> {{ $mascota->sexo ?? '-' }}<br>
            <label>Edad:</label> {{ $mascota->fecha_nacimiento ? \Carbon\Carbon::parse($mascota->fecha_nacimiento)->age . ' años' : '-' }}<br>
            <label>Peso:</label> {{ $mascota->peso ? $mascota->peso . ' kg' : '-' }}<br>
            <label>Color:</label> {{ $mascota->color ?? '-' }}<br>
            <label>Temperamento:</label> {{ ucfirst($mascota->temperamento_general ?? '-') }}<br>
            <label>Alergias:</label> {{ $mascota->alergias ?? '-' }}<br>
            <label>Cuidados especiales:</label> {{ $mascota->cuidados_especiales ?? '-' }}<br>
            <label>Observaciones:</label> {{ $mascota->observaciones ?? '-' }}<br>
            <label>Estado:</label> {{ $mascota->estado == 'activa' ? '✅ Activa' : '❌ Inactiva' }}<br>
        </div>
        
        @if(isset($rol) && $rol == 'admin')
    <a href="/admin/mascotas?token={{ $token }}" class="btn btn-back">← Volver</a>
@elseif(isset($rol) && $rol == 'recepcion')
    <a href="/recepcion/mascotas?token={{ $token }}" class="btn btn-back">← Volver</a>
@else
    <a href="/cliente/mascotas?token={{ $token }}" class="btn btn-back">← Volver</a>
@endif
        
        <a href="/cliente/mascotas/{{ $mascota->id_mascota }}/edit?token={{ $token }}" class="btn btn-edit">✏️ Editar</a>
    </div>
</body>
</html>