<!DOCTYPE html>
<html>
<head>
    <title>Mis Mascotas - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #fff3e0; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .mascota-card { border: 1px solid #ddd; border-radius: 10px; padding: 15px; margin: 10px; display: inline-block; width: 250px; vertical-align: top; }
        .mascota-card img { width: 100%; height: 150px; object-fit: cover; border-radius: 10px; }
        .btn { background: #4CAF50; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn-edit { background: #2196F3; }
        .btn-delete { background: #f44336; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .no-mascotas { text-align: center; padding: 50px; background: #f9f9f9; border-radius: 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐕 Mis Mascotas</h1>
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        
        @php
            $token = request()->query('token');
        @endphp
        
        <a href="/cliente/mascotas/create?token={{ $token }}" class="btn">+ Agregar Mascota</a>
        <a href="/cliente/dashboard?token={{ $token }}" style="margin-left: 10px;">← Volver</a>
        
        <div style="margin-top: 20px;">
            @if(count($mascotas) > 0)
                @foreach($mascotas as $mascota)
                <div class="mascota-card">
                    @if($mascota->foto)
                        <img src="{{ asset('storage/' . $mascota->foto) }}" alt="{{ $mascota->nombre }}">
                    @else
                        <div style="background: #eee; height: 150px; display: flex; align-items: center; justify-content: center;">🐾</div>
                    @endif
                    <h3>{{ $mascota->nombre }}</h3>
                    <p><strong>Especie:</strong> {{ $mascota->especie }}</p>
                    <p><strong>Raza:</strong> {{ $mascota->raza ?? '-' }}</p>
                    <a href="/cliente/mascotas/{{ $mascota->id_mascota }}?token={{ $token }}" class="btn">📋 Ficha</a>
                    <a href="/cliente/mascotas/{{ $mascota->id_mascota }}/edit?token={{ $token }}" class="btn btn-edit">✏️ Editar</a>
                </div>
                @endforeach
            @else
                <div class="no-mascotas">
                    <p>🐾 No tienes mascotas registradas aún.</p>
                    <a href="/cliente/mascotas/create?token={{ $token }}" class="btn">➕ Registrar mi primera mascota</a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>