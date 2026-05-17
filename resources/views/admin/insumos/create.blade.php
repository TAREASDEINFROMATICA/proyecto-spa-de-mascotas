<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Insumo</title>
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
        <h1>➕ Nuevo Insumo</h1>
        
        <div style="background: #e3f2fd; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            📋 Complete los datos del insumo. Podrá agregar la imagen después.
        </div>
        
        <form method="POST" action="{{ route('admin.insumos.store', ['token' => $token]) }}">
            @csrf
            
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" required placeholder="Ej: Shampoo Hipoalergénico">
            </div>
            
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" rows="3" placeholder="Descripción del insumo..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Stock Inicial *</label>
                <input type="number" name="stock" step="0.01" value="0" required>
            </div>
            
            <div class="form-group">
                <label>Stock Mínimo *</label>
                <input type="number" name="stock_minimo" step="0.01" value="0" required>
                <small>Alertar cuando el stock baje de este valor</small>
            </div>
            
            <div class="form-group">
                <label>Unidad de Medida *</label>
                <select name="unidad_medida" required>
                    <option value="ml">Mililitros (ml)</option>
                    <option value="l">Litros (l)</option>
                    <option value="g">Gramos (g)</option>
                    <option value="kg">Kilogramos (kg)</option>
                    <option value="unidad">Unidad</option>
                    <option value="par">Par</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Costo Unitario (Bs)</label>
                <input type="number" name="costo_unitario" step="0.01" placeholder="0.00">
            </div>
            
            <button type="submit" class="btn">💾 Guardar Insumo</button>
            <a href="{{ route('admin.insumos.index', ['token' => $token]) }}" class="btn btn-volver">← Cancelar</a>
        </form>
    </div>
</body>
</html>