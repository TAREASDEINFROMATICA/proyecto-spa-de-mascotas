<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Insumos</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #4CAF50; color: white; }
        .btn { background: #4CAF50; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 2px; border: none; cursor: pointer; font-size: 12px; }
        .btn-danger { background: #f44336; }
        .btn-warning { background: #ff9800; }
        .btn-info { background: #2196F3; }
        .stock-bajo { color: #f44336; font-weight: bold; }
        .stock-normal { color: #4CAF50; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; align-items: center; flex-wrap: wrap; }
        .estado-activo { background: #4CAF50; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
        .estado-inactivo { background: #999; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
        .estado-null { background: #f44336; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
        .filtros { display: flex; gap: 10px; margin-bottom: 15px; }
        .filtros input, .filtros select { padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧴 Gestión de Insumos</h1>
            <div>
                <a href="{{ route('admin.insumos.create', ['token' => $token]) }}" class="btn">+ Nuevo Insumo</a>
                <a href="/admin/dashboard?token={{ $token }}" class="btn" style="background: #607d8b;">← Volver</a>
            </div>
        </div>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div class="filtros">
            <input type="text" id="searchInput" placeholder="🔍 Buscar insumo..." onkeyup="filtrarTabla()">
            <select id="filtroStock" onchange="filtrarTabla()">
                <option value="todos">Todos los insumos</option>
                <option value="bajo">Stock bajo</option>
                <option value="sin">Sin stock</option>
                <option value="normal">Con stock</option>
            </select>
        </div>

        <table id="tablaInsumos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Stock</th>
                    <th>Stock Mínimo</th>
                    <th>Unidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($insumos as $insumo)
                <tr data-stock="{{ $insumo->stock }}" data-stock-minimo="{{ $insumo->stock_minimo }}">
                    <td>{{ $insumo->id_insumo }}</td>
                    <td class="nombre">{{ $insumo->nombre }}</td>
                    <td class="stock {{ $insumo->stock <= $insumo->stock_minimo ? 'stock-bajo' : 'stock-normal' }}">
                        {{ $insumo->stock }} {{ $insumo->unidad_medida }}
                    </td>
                    <td>{{ $insumo->stock_minimo }} {{ $insumo->unidad_medida }}</td>
                    <td>{{ $insumo->unidad_medida }}</td>
                    <td>
                        <span class="
                            @if($insumo->estado === 'activo') estado-activo
                            @elseif($insumo->estado === 'inactivo') estado-inactivo
                            @else estado-null
                            @endif
                        ">
                            {{ $insumo->estado ?? 'sin estado' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.insumos.edit', ['id' => $insumo->id_insumo, 'token' => $token]) }}" class="btn btn-info">✏️ Editar</a>
                        <a href="{{ route('admin.insumos.toggle', ['id' => $insumo->id_insumo, 'token' => $token]) }}" class="btn {{ $insumo->estado === 'activo' ? 'btn-danger' : 'btn-warning' }}">
                            {{ $insumo->estado === 'activo' ? '🔴 Desactivar' : '🟢 Activar' }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            {{ $insumos->appends(['token' => $token])->links() }}
        </div>
    </div>

    <script>
        function filtrarTabla() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const filtroStock = document.getElementById('filtroStock').value;
            const rows = document.querySelectorAll('#tablaInsumos tbody tr');
            
            rows.forEach(row => {
                const nombre = row.querySelector('.nombre')?.textContent.toLowerCase() || '';
                const stock = parseFloat(row.dataset.stock);
                const stockMinimo = parseFloat(row.dataset.stockMinimo);
                
                let mostrar = true;
                
                // Filtro por nombre
                if (searchTerm && !nombre.includes(searchTerm)) {
                    mostrar = false;
                }
                
                // Filtro por stock
                if (mostrar && filtroStock !== 'todos') {
                    if (filtroStock === 'bajo') {
                        mostrar = stock <= stockMinimo && stock > 0;
                    } else if (filtroStock === 'sin') {
                        mostrar = stock === 0;
                    } else if (filtroStock === 'normal') {
                        mostrar = stock > stockMinimo;
                    }
                }
                
                row.style.display = mostrar ? '' : 'none';
            });
        }
    </script>
</body>
</html>