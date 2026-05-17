<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Productos</title>
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
            max-width: 1400px;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h1 {
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header h1 i {
            font-size: 32px;
        }
        
        .header-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }
        
        .btn-primary {
            background: white;
            color: #2196F3;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-warning {
            background: #ff9800;
            color: white;
        }
        
        .btn-warning:hover {
            background: #f57c00;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .btn-secondary:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .btn-edit {
            background: #2196F3;
            color: white;
        }
        
        .btn-edit:hover {
            background: #1976D2;
        }
        
        .btn-toggle {
            background: #f44336;
            color: white;
        }
        
        .btn-toggle:hover {
            background: #d32f2f;
        }
        
        .content {
            padding: 30px;
        }
        
        .success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #28a745;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .stock-bajo {
            color: #f44336;
            font-weight: 700;
        }
        
        .estado-activo {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .estado-inactivo {
            background: #9e9e9e;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 8px;
            margin: 0 3px;
        }
        
        .pagination {
            margin-top: 25px;
            display: flex;
            justify-content: center;
        }
        
        .pagination nav {
            display: inline-block;
        }
        
        .pagination .page-item {
            display: inline-block;
            margin: 0 3px;
        }
        
        .pagination .page-link {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .pagination .page-link:hover {
            background: #2196F3;
            color: white;
            border-color: #2196F3;
        }
        
        .imagen-producto {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 8px;
            }
            
            .btn-small {
                padding: 4px 8px;
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-store"></i>
                Gestión de Productos
            </h1>
            <div class="header-buttons">
                <a href="#" id="enlaceNuevoProducto" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Nuevo Producto
                </a>
                <a href="#" id="enlaceGestionCategorias" class="btn btn-warning">
                    <i class="fas fa-folder"></i> Gestionar Categorías
                </a>
                <a href="/admin/dashboard?token={{ $token }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="content">
            @if(session('success'))
                <div class="success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Categoría</th>
                            <th>Nombre</th>
                            <th>Precio Venta</th>
                            <th>Stock</th>
                            <th>Unidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                        <tr>
                            <td>{{ $producto->id_producto_venta }}</td>
                            <td>
                                @if($producto->imagen)
                                    <img src="{{ Storage::url($producto->imagen) }}" class="imagen-producto" alt="{{ $producto->nombre }}">
                                @else
                                    <i class="fas fa-image" style="font-size: 30px; color: #ccc;"></i>
                                @endif
                            </td>
                            <td>{{ $producto->categoria->nombre ?? '-' }}</td>
                            <td><strong>{{ $producto->nombre }}</strong></td>
                            <td><span style="color: #4CAF50; font-weight: 600;">Bs {{ number_format($producto->precio_venta, 2) }}</span></td>
                            <td class="{{ $producto->stock <= $producto->stock_minimo ? 'stock-bajo' : '' }}">
                                <i class="fas fa-boxes"></i> {{ $producto->stock }} {{ $producto->unidad_medida }}
                                @if($producto->stock <= $producto->stock_minimo)
                                    <br><small style="color: #f44336;">⚠️ Stock bajo</small>
                                @endif
                            </td>
                            <td>{{ $producto->unidad_medida }}</td>
                            <td>
                                <span class="{{ $producto->estado === 'activo' ? 'estado-activo' : 'estado-inactivo' }}">
                                    <i class="fas {{ $producto->estado === 'activo' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $producto->estado === 'activo' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <a href="#" class="btn btn-edit btn-small btn-editar" data-id="{{ $producto->id_producto_venta }}">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="#" class="btn btn-toggle btn-small btn-toggle" data-id="{{ $producto->id_producto_venta }}" data-estado="{{ $producto->estado }}">
                                    <i class="fas {{ $producto->estado === 'activo' ? 'fa-ban' : 'fa-check' }}"></i>
                                    {{ $producto->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($productos->hasPages())
                <div class="pagination">
                    {{ $productos->appends(['token' => $token])->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
        
        // Enlace para Gestionar Categorías
        const enlaceGestionCategorias = document.getElementById('enlaceGestionCategorias');
        if (enlaceGestionCategorias) {
            enlaceGestionCategorias.href = '/admin/categorias?token=' + token;
        }
        
        // Enlace para Nuevo Producto
        const enlaceNuevoProducto = document.getElementById('enlaceNuevoProducto');
        if (enlaceNuevoProducto) {
            enlaceNuevoProducto.href = '/admin/productos/create?token=' + token;
        }
        
        // Enlaces dinámicos para editar productos
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                window.location.href = '/admin/productos/' + id + '/edit?token=' + token;
            });
        });
        
        // Enlaces para activar/desactivar productos
        document.querySelectorAll('.btn-toggle').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const estado = this.getAttribute('data-estado');
                const accion = estado === 'activo' ? 'desactivar' : 'activar';
                if (confirm(`¿Estás seguro de ${accion} este producto?`)) {
                    window.location.href = '/admin/productos/' + id + '/toggle?token=' + token;
                }
            });
        });
    </script>
</body>
</html>