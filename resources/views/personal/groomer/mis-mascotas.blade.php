<!DOCTYPE html>
<html>
<head>
    <title>Mis Mascotas - Groomer</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .mascota-card { border: 1px solid #ddd; border-radius: 10px; padding: 15px; margin: 10px; display: inline-block; width: 200px; }
        .btn { background: #607d8b; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐕 Mascotas Asignadas</h1>
        
        @php $token = request()->query('token'); @endphp
        
        @if($mascotas->count() > 0)
            @foreach($mascotas as $mascota)
            <div class="mascota-card">
                <h3>{{ $mascota->nombre }}</h3>
                <p><strong>Especie:</strong> {{ $mascota->especie }}</p>
                <p><strong>Raza:</strong> {{ $mascota->raza ?? '-' }}</p>
                <p><strong>Dueño:</strong> {{ $mascota->cliente->usuario->nombres }}</p>
            </div>
            @endforeach
        @else
            <p>No tienes mascotas asignadas actualmente.</p>
        @endif
        
        <a href="/groomer/dashboard?token={{ $token }}" class="btn">← Volver al Dashboard</a>
    </div>
</body>
</html>