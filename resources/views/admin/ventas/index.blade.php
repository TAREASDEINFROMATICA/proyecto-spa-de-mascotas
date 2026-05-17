<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Ventas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-primary { background: white; color: #4CAF50; }
        .btn-primary:hover { transform: translateY(-2px); }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .content { padding: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f9f9f9; }
        .estado-pagada { background: #e8f5e9; color: #4CAF50; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .total { font-weight: 700; color: #4CAF50; }
        @media (max-width: 768px) { .header { flex-direction: column; text-align: center; } table { font-size: 12px; } th, td { padding: 8px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> Gestión de Ventas</h1>
            <div>
                <a href="/admin/ventas/create?token={{ $token }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Venta</a>
                <a href="/admin/dashboard?token={{ $token }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
            </div>
        </div>
        
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $venta)
                    <tr>
                        <td>{{ $venta->id_venta }}</td>
                        <td>{{ $venta->cliente->usuario->nombres }} {{ $venta->cliente->usuario->apellidos }}</td>
                        <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}</td>
                        <td class="total">Bs {{ number_format($venta->total, 2) }}</td>
                        <td><span class="estado-pagada"><i class="fas fa-check-circle"></i> Pagada</span></td>
                        <td><a href="/admin/ventas/{{ $venta->id_venta }}?token={{ $token }}" ><i class="fas fa-eye"></i> Ver</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $ventas->appends(['token' => $token])->links() }}
        </div>
    </div>
</body>
</html>