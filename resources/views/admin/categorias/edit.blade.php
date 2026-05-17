<!DOCTYPE html>
<html>
<head>
    <title>Editar Categoría - {{ $categoria->nombre }}</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-volver { background: #607d8b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Editar Categoría</h1>
        
        <form method="POST" action="{{ route('admin.categorias.update', ['id' => $categoria->id_categoria, 'token' => $token]) }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" value="{{ $categoria->nombre }}" required>
            </div>
            
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" rows="3">{{ $categoria->descripcion }}</textarea>
            </div>
            
            <button type="submit" class="btn">💾 Guardar Cambios</button>
            <a href="{{ route('admin.categorias.index', ['token' => $token]) }}" class="btn btn-volver">← Cancelar</a>
        </form>
    </div>
</body>
</html>