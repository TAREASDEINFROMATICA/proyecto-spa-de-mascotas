<!DOCTYPE html>
<html>
<head>
    <title>Mis Compras - Pet Spa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .btn-primary { background: white; color: #9C27B0; }
        .btn-primary:hover { transform: translateY(-2px); }
        .content { padding: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        tr:hover { background: #f9f9f9; }
        .estado-pagada { background: #e8f5e9; color: #4CAF50; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .total { font-weight: 700; color: #4CAF50; }
        .btn-ver { background: #2196F3; color: white; padding: 6px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; }
        .btn-ver:hover { background: #1976D2; }
        .empty { text-align: center; padding: 60px; color: #999; }
        .empty i { font-size: 64px; margin-bottom: 20px; display: block; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; }
        @media (max-width: 768px) { .header { flex-direction: column; text-align: center; } table { font-size: 12px; } th, td { padding: 8px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-history"></i> Mis Compras</h1>
            <div>
                <a href="/cliente/tienda?token={{ $token }}" class="btn btn-primary">
                    <i class="fas fa-store"></i> Seguir Comprando
                </a>
                <a href="/cliente/dashboard?token={{ $token }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="content">
            @if($ventas->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>N° Venta</th>
                        <th>Fecha</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $venta)
                    <tr>
                        <td><strong>#{{ $venta->id_venta }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}</td>
                        <td>
                            @foreach($venta->detalles as $detalle)
                                {{ $detalle->cantidad }}x {{ $detalle->producto->nombre }}<br>
                            @endforeach
                        </td>
                        <td class="total">Bs {{ number_format($venta->total, 2) }}</td>
                        <td><span class="estado-pagada"><i class="fas fa-check-circle"></i> Pagada</span></td>
                        <td>
                            <a href="/cliente/mis-compras/{{ $venta->id_venta }}?token={{ $token }}" class="btn-ver">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">
                {{ $ventas->appends(['token' => $token])->links() }}
            </div>
            @else
                <div class="empty">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>No has realizado compras aún</h3>
                    <p>¡Explora nuestra tienda y consiente a tu mascota!</p>
                    <a href="/cliente/tienda?token={{ $token }}" class="btn btn-primary" style="margin-top: 20px; display: inline-block;">
                        <i class="fas fa-store"></i> Ir a la Tienda
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>