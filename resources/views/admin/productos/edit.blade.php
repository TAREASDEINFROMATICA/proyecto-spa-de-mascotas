<!DOCTYPE html>
<html>
<head>
    <title>Editar Producto - {{ $producto->nombre }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .content {
            padding: 30px;
        }
        
        .stock-card {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .stock-card .label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .stock-card .value {
            font-size: 32px;
            font-weight: 700;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        label i {
            margin-right: 8px;
            color: #2196F3;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #2196F3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }
        
        small {
            display: block;
            margin-top: 5px;
            color: #888;
            font-size: 12px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
        }
        
        .btn-secondary {
            background: #607d8b;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #546e7a;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            color: white;
        }
        
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3);
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        
        hr {
            margin: 30px 0;
            border: none;
            height: 1px;
            background: linear-gradient(to right, #e0e0e0, transparent);
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: #ff9800;
            font-size: 22px;
        }
        
        .imagen-actual {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            margin-top: 10px;
        }
        
        .imagen-actual img {
            max-width: 150px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .ajuste-stock {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 16px;
            margin-top: 10px;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-info {
            background: #e3f2fd;
            color: #1976d2;
            border-left: 4px solid #1976d2;
        }
        
        .alert-info i {
            font-size: 20px;
        }
        
        .categoria-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                justify-content: center;
            }
            
            .stock-card {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-box"></i>
                Editar Producto
            </h1>
            <p>Actualiza la información del producto para la tienda</p>
        </div>
        
        <div class="content">
            <!-- Stock actual -->
            <div class="stock-card">
                <div>
                    <div class="label">
                        <i class="fas fa-boxes"></i> Stock Actual
                    </div>
                    <div class="value">{{ $producto->stock }} {{ $producto->unidad_medida }}</div>
                </div>
                <div>
                    <i class="fas fa-chart-line" style="font-size: 40px; opacity: 0.5;"></i>
                </div>
            </div>
            
            <!-- Formulario principal -->
            <form method="POST" action="{{ route('admin.productos.update', ['id' => $producto->id_producto_venta, 'token' => $token]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label><i class="fas fa-tags"></i> Categoría *</label>
                    <select name="id_categoria" required>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id_categoria }}" {{ $producto->id_categoria == $categoria->id_categoria ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nombre *</label>
                    <input type="text" name="nombre" value="{{ $producto->nombre }}" required placeholder="Ej: Alimento Premium para Perros">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Descripción</label>
                    <textarea name="descripcion" rows="3" placeholder="Descripción del producto...">{{ $producto->descripcion }}</textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-shopping-cart"></i> Precio Compra (Bs)</label>
                    <input type="number" name="precio_compra" step="0.01" value="{{ $producto->precio_compra }}" placeholder="0.00">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-dollar-sign"></i> Precio Venta (Bs) *</label>
                    <input type="number" name="precio_venta" step="0.01" value="{{ $producto->precio_venta }}" required placeholder="0.00">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-exclamation-triangle"></i> Stock Mínimo *</label>
                    <input type="number" name="stock_minimo" value="{{ $producto->stock_minimo }}" required>
                    <small>⚠️ Alertar cuando el stock baje de este valor</small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-ruler"></i> Unidad de Medida *</label>
                    <select name="unidad_medida" required>
                        <option value="unidad" {{ $producto->unidad_medida == 'unidad' ? 'selected' : '' }}>Unidad</option>
                        <option value="kg" {{ $producto->unidad_medida == 'kg' ? 'selected' : '' }}>Kilogramo (kg)</option>
                        <option value="g" {{ $producto->unidad_medida == 'g' ? 'selected' : '' }}>Gramo (g)</option>
                        <option value="l" {{ $producto->unidad_medida == 'l' ? 'selected' : '' }}>Litro (l)</option>
                        <option value="ml" {{ $producto->unidad_medida == 'ml' ? 'selected' : '' }}>Mililitro (ml)</option>
                    </select>
                </div>
                
                <!-- Imagen actual -->
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Imagen Actual</label>
                    <div class="imagen-actual">
                        @if($producto->imagen)
                            <img src="{{ Storage::url($producto->imagen) }}" alt="{{ $producto->nombre }}">
                            <p style="margin-top: 10px; font-size: 12px; color: #666;">
                                <i class="fas fa-check-circle" style="color: #4CAF50;"></i> Imagen actual del producto
                            </p>
                        @else
                            <i class="fas fa-camera" style="font-size: 48px; color: #ccc;"></i>
                            <p style="margin-top: 10px; color: #999;">No hay imagen cargada</p>
                        @endif
                    </div>
                </div>
                
                <!-- Subir nueva imagen -->
                <div class="form-group">
                    <label><i class="fas fa-upload"></i> Nueva Imagen (opcional)</label>
                    <input type="file" name="imagen" accept="image/*">
                    <small>📷 Formatos: JPG, PNG, GIF (Max 2MB). Dejar vacío para mantener la imagen actual</small>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('admin.productos.index', ['token' => $token]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                </div>
            </form>
            
            <hr>
            
            <!-- Sección de ajuste de stock -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <div>Aquí puedes ajustar el stock manualmente para entradas (compras) o salidas (ventas, mermas, devoluciones).</div>
            </div>
            
            <div class="section-title">
                <i class="fas fa-sliders-h"></i>
                <span>Ajustar Stock Manualmente</span>
            </div>
            
            <div class="ajuste-stock">
                <div class="form-group">
                    <label><i class="fas fa-exchange-alt"></i> Tipo de movimiento</label>
                    <select id="tipo_movimiento" required>
                        <option value="entrada">
                            ➕ Entrada (agregar stock)
                        </option>
                        <option value="salida">
                            ➖ Salida (retirar stock)
                        </option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Cantidad</label>
                    <input type="number" id="cantidad" step="1" min="1" placeholder="Ej: 10" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-comment"></i> Motivo</label>
                    <input type="text" id="motivo" placeholder="Ej: Compra, Devolución, Venta, Merma, etc.">
                </div>
                
                <button type="button" onclick="ajustarStock()" class="btn btn-warning" style="width: 100%;">
                    <i class="fas fa-sync-alt"></i> Aplicar Ajuste
                </button>
            </div>
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
        const productoId = '{{ $producto->id_producto_venta }}';
        
        function ajustarStock() {
            const tipo = document.getElementById('tipo_movimiento').value;
            const cantidad = parseInt(document.getElementById('cantidad').value);
            const motivo = document.getElementById('motivo').value;
            
            if (!cantidad || cantidad <= 0) {
                alert('❌ Ingrese una cantidad válida');
                return;
            }
            
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            
            fetch('/admin/productos/' + productoId + '/ajustar-stock?token=' + token, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tipo: tipo,
                    cantidad: cantidad,
                    motivo: motivo
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Error: ' + data.message);
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Aplicar Ajuste';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error de conexión');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Aplicar Ajuste';
            });
        }
    </script>
</body>
</html>