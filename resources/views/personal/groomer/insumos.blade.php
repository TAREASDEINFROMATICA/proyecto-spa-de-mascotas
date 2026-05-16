<!DOCTYPE html>
<html>
<head>
    <title>Insumos - Groomer</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .btn { background: #607d8b; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Mis Insumos</h1>
        
        @php $token = request()->query('token'); @endphp
        
        <div class="info">
            <p>🧴 Esta sección te permitirá registrar los insumos utilizados en cada servicio.</p>
            <p>🚧 En construcción - Próximamente disponible.</p>
        </div>
        
        <a href="/groomer/dashboard?token={{ $token }}" class="btn">← Volver al Dashboard</a>
    </div>
</body>
</html>