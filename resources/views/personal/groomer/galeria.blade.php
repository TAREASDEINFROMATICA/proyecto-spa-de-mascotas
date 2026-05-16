<!DOCTYPE html>
<html>
<head>
    <title>Galería - Groomer</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .fotos { display: flex; flex-wrap: wrap; gap: 15px; }
        .foto-card { width: 200px; border: 1px solid #ddd; border-radius: 8px; padding: 10px; text-align: center; }
        .foto-card img { width: 100%; height: 150px; object-fit: cover; border-radius: 5px; }
        .btn { background: #607d8b; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📸 Galería de Fotos</h1>
        
        @php $token = request()->query('token'); @endphp
        
        <div class="fotos">
            @forelse($fotos as $foto)
            <div class="foto-card">
                <img src="{{ Storage::url($foto->url) }}" alt="Foto">
                <p>Tipo: {{ ucfirst($foto->tipo) }}</p>
            </div>
            @empty
            <p>No hay fotos disponibles.</p>
            @endforelse
        </div>
        
        <a href="/groomer/dashboard?token={{ $token }}" class="btn">← Volver al Dashboard</a>
    </div>
</body>
</html>