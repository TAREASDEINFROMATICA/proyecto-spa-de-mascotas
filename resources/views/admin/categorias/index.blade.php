<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Categorías</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #4CAF50; color: white; }
        .btn { background: #4CAF50; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 2px; }
        .btn-edit { background: #2196F3; }
        .btn-delete { background: #f44336; }
        .btn-volver { background: #607d8b; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; align-items: center; flex-wrap: wrap; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📂 Gestión de Categorías</h1>
            <div>
                <a href="{{ route('admin.categorias.create', ['token' => $token]) }}" class="btn">+ Nueva Categoría</a>
                <a href="/admin/productos?token={{ $token }}" class="btn" style="background: #4CAF50;">← Volver a Productos</a>
            </div>
        </div>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Productos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categorias as $categoria)
                <tr>
                    <td>{{ $categoria->id_categoria }}</td>
                    <td>{{ $categoria->nombre }}</td>
                    <td>{{ $categoria->descripcion ?? '-' }}</td>
                    <td>{{ $categoria->productos()->count() }}</td>
                    <td>
                        <a href="{{ route('admin.categorias.edit', ['id' => $categoria->id_categoria, 'token' => $token]) }}" class="btn btn-edit">✏️ Editar</a>
                        <form action="{{ route('admin.categorias.destroy', ['id' => $categoria->id_categoria, 'token' => $token]) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta categoría?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete">🗑️ Eliminar</button>
                        </form>
                     </td>
                 </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $categorias->appends(['token' => $token])->links() }}
        </div>
    </div>
</body>
</html>