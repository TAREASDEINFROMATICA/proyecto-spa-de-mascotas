<!DOCTYPE html>
<html>
<head>
    <title>Mis Compras - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        
        /* Estados del pedido */
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .badge-pendiente { background: #f5f5f5; color: #757575; }
        .badge-confirmado { background: #e3f2fd; color: #1976d2; }
        .badge-preparando { background: #fff3e0; color: #f57c00; }
        .badge-listo_para_recoger { background: #e8f5e9; color: #4caf50; }
        .badge-entregado { background: #f3e5f5; color: #9c27b0; }
        .badge-cancelado { background: #ffebee; color: #f44336; }
        
        .total { font-weight: 700; color: #4CAF50; }
        .btn-ver { background: #2196F3; color: white; padding: 6px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; }
        .btn-ver:hover { background: #1976D2; }
        .empty { text-align: center; padding: 60px; color: #999; }
        .empty i { font-size: 64px; margin-bottom: 20px; display: block; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; }
        .pagination a, .pagination span { margin: 0 5px; padding: 8px 12px; border-radius: 8px; text-decoration: none; color: #666; }
        .pagination .active span { background: #9C27B0; color: white; }
        .productos-list { font-size: 13px; line-height: 1.5; }
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
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>N° Pedido</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $estadosBadge = [
                                'pendiente' => ['texto' => '📋 Pendiente', 'clase' => 'badge-pendiente'],
                                'confirmado' => ['texto' => '✅ Confirmado', 'clase' => 'badge-confirmado'],
                                'preparando' => ['texto' => '🔧 Preparando', 'clase' => 'badge-preparando'],
                                'listo_para_recoger' => ['texto' => '📦 Listo para recoger', 'clase' => 'badge-listo_para_recoger'],
                                'entregado' => ['texto' => '🎉 Entregado', 'clase' => 'badge-entregado'],
                                'cancelado' => ['texto' => '❌ Cancelado', 'clase' => 'badge-cancelado'],
                            ];
                        @endphp
                        @foreach($ventas as $venta)
                        <tr>
                            <td><strong>#{{ $venta->id_venta }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}</td>
                            <td class="productos-list">
                                @foreach($venta->detalles as $detalle)
                                    {{ $detalle->cantidad }}x {{ $detalle->producto->nombre }}<br>
                                @endforeach
                            </td>
                            <td class="total">Bs {{ number_format($venta->total, 2) }}</td>
                            <td>
                                @php
                                    $estadoInfo = $estadosBadge[$venta->estado_pedido] ?? ['texto' => '📋 Pendiente', 'clase' => 'badge-pendiente'];
                                @endphp
                                <span class="badge {{ $estadoInfo['clase'] }}">
                                    {{ $estadoInfo['texto'] }}
                                </span>
                            </td>
                            <td>
                                <a href="/cliente/mis-compras/{{ $venta->id_venta }}?token={{ $token }}" class="btn-ver">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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