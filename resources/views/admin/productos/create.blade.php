<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Producto</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-volver { background: #607d8b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Nuevo Producto</h1>
        
        <div style="background: #e3f2fd; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            📋 Complete los datos del producto. Podrá agregar la imagen después.
        </div>
        
        <form method="POST" action="{{ route('admin.productos.store', ['token' => $token]) }}">
            @csrf
            
            <div class="form-group">
                <label>Categoría *</label>
                <select name="id_categoria" required>
                    <option value="">-- Seleccionar --</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id_categoria }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label>Precio Compra (Bs)</label>
                <input type="number" name="precio_compra" step="0.01">
            </div>
            
            <div class="form-group">
                <label>Precio Venta (Bs) *</label>
                <input type="number" name="precio_venta" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label>Stock Inicial *</label>
                <input type="number" name="stock" value="0" required>
            </div>
            
            <div class="form-group">
                <label>Stock Mínimo *</label>
                <input type="number" name="stock_minimo" value="0" required>
            </div>
            
            <div class="form-group">
                <label>Unidad de Medida *</label>
                <select name="unidad_medida" required>
                    <option value="unidad">Unidad</option>
                    <option value="kg">Kilogramo</option>
                    <option value="g">Gramo</option>
                    <option value="l">Litro</option>
                    <option value="ml">Mililitro</option>
                </select>
            </div>
            
            <button type="submit" class="btn">💾 Guardar Producto</button>
            <a href="{{ route('admin.productos.index', ['token' => $token]) }}" class="btn btn-volver">← Cancelar</a>
        </form>
    </div>
</body>
</html>