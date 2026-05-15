<!DOCTYPE html>
<html>
<head>
    <title>Detalle Cliente - Recepción</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #e3f2fd; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        .info { margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 5px; }
        .info label { font-weight: bold; display: inline-block; width: 120px; }
        .btn { background: #607d8b; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn-ver { background: #4CAF50; }
        h2 { color: #2196F3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👤 Detalle del Cliente</h1>
        
        @php $token = request()->query('token'); @endphp
        
        <div class="info">
            <label>Nombre:</label> {{ $cliente->nombres }} {{ $cliente->apellidos }}<br>
            <label>Email:</label> {{ $cliente->correo }}<br>
            <label>Teléfono:</label> {{ $cliente->telefono ?? '-' }}<br>
            <label>CI:</label> {{ $cliente->ci ?? '-' }}<br>
            <label>Dirección:</label> {{ $cliente->cliente->direccion ?? '-' }}<br>
            <label>Estado:</label> {{ $cliente->estado == 'activo' ? '✅ Activo' : '❌ Inactivo' }}<br>
            <label>Registro:</label> {{ \Carbon\Carbon::parse($cliente->fecha_registro)->format('d/m/Y H:i') }}
        </div>
        
        <h2>🐕 Mascotas del Cliente</h2>
        @if($mascotas->count() > 0)
            <ul>
                @foreach($mascotas as $mascota)
                <li><strong>{{ $mascota->nombre }}</strong> - {{ $mascota->especie }} ({{ $mascota->raza ?? 'Sin raza' }})</li>
                @endforeach
            </ul>
        @else
            <p>No tiene mascotas registradas.</p>
        @endif
        
        <a href="/recepcion/clientes?token={{ $token }}" class="btn">← Volver a Clientes</a>
        <a href="/recepcion/mascotas?token={{ $token }}" class="btn btn-ver">🐕 Ver todas las mascotas</a>
    </div>
</body>
</html>