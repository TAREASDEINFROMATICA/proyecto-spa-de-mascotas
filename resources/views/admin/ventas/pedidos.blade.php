<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Pedidos - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 35px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h2 { display: flex; align-items: center; gap: 10px; }
        .content { padding: 30px; }
        .nav-tabs { display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
        .nav-tab { padding: 8px 16px; border-radius: 20px; text-decoration: none; color: #64748b; font-weight: 500; transition: 0.3s; }
        .nav-tab:hover { background: #f1f5f9; color: #1e293b; }
        .nav-tab.active { background: #4CAF50; color: white; }
        .card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 20px; overflow: hidden; transition: 0.3s; }
        .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card-header { background: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-pendiente { background: #ffebee; color: #c62828; }
        .badge-confirmado { background: #e3f2fd; color: #1565c0; }
        .badge-preparando { background: #fff3e0; color: #ef6c00; }
        .badge-listo_para_recoger { background: #e8f5e9; color: #2e7d32; }
        .badge-entregado { background: #e0f2f1; color: #00695c; }
        .badge-cancelado { background: #fafafa; color: #9e9e9e; }
        .card-body { padding: 20px; }
        .btn { padding: 8px 16px; border-radius: 10px; border: none; cursor: pointer; font-weight: 500; transition: 0.3s; }
        .btn-success { background: #4CAF50; color: white; }
        .btn-success:hover { background: #45a049; transform: translateY(-2px); }
        .btn-primary { background: #2196F3; color: white; }
        .btn-primary:hover { background: #1976D2; transform: translateY(-2px); }
        select { padding: 8px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; cursor: pointer; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(500px, 1fr)); gap: 20px; }
        .productos { margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #64748b; }
        @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @php $token = request()->query('token'); @endphp
    
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-box"></i> Gestión de Pedidos</h2>
            <a href="/admin/dashboard?token={{ $token }}" class="btn btn-primary" style="background: rgba(255,255,255,0.2); color: white;">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <div class="content">
            <div class="nav-tabs">
                <a href="?token={{ $token }}" class="nav-tab {{ !$estado ? 'active' : '' }}">Todos</a>
                <a href="?estado=pendiente&token={{ $token }}" class="nav-tab {{ $estado == 'pendiente' ? 'active' : '' }}">Pendientes</a>
                <a href="?estado=confirmado&token={{ $token }}" class="nav-tab {{ $estado == 'confirmado' ? 'active' : '' }}">Confirmados</a>
                <a href="?estado=preparando&token={{ $token }}" class="nav-tab {{ $estado == 'preparando' ? 'active' : '' }}">Preparando</a>
                <a href="?estado=listo_para_recoger&token={{ $token }}" class="nav-tab {{ $estado == 'listo_para_recoger' ? 'active' : '' }}">Listos para recoger</a>
                <a href="?estado=entregado&token={{ $token }}" class="nav-tab {{ $estado == 'entregado' ? 'active' : '' }}">Entregados</a>
            </div>
            
            <div class="grid">
                @foreach($ventas as $venta)
                <div class="card">
                    <div class="card-header">
                        <strong>📄 Pedido #{{ $venta->id_venta }}</strong>
                        <span class="badge badge-{{ str_replace('_', '', $venta->estado_pedido) }}">
                            {{ ucfirst(str_replace('_', ' ', $venta->estado_pedido)) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <p><strong>👤 Cliente:</strong> {{ $venta->cliente->usuario->nombres }} {{ $venta->cliente->usuario->apellidos }}</p>
                        <p><strong>📧 Email:</strong> {{ $venta->cliente->usuario->correo }}</p>
                        <p><strong>💰 Total:</strong> <span style="color: #4CAF50; font-weight: bold;">Bs {{ number_format($venta->total, 2) }}</span></p>
                        <p><strong>📅 Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}</p>
                        
                        <div class="productos">
                            <strong>🛍️ Productos:</strong><br>
                            @foreach($venta->detalles as $detalle)
                            - {{ $detalle->producto->nombre }} x{{ $detalle->cantidad }} = Bs {{ number_format($detalle->subtotal, 2) }}<br>
                            @endforeach
                        </div>
                        
                        <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                            <select class="estado-select" data-id="{{ $venta->id_venta }}" style="flex: 1;">
                                <option value="pendiente" {{ $venta->estado_pedido == 'pendiente' ? 'selected' : '' }}>📋 Pendiente</option>
                                <option value="confirmado" {{ $venta->estado_pedido == 'confirmado' ? 'selected' : '' }}>✅ Confirmado</option>
                                <option value="preparando" {{ $venta->estado_pedido == 'preparando' ? 'selected' : '' }}>🔧 Preparando</option>
                                <option value="listo_para_recoger" {{ $venta->estado_pedido == 'listo_para_recoger' ? 'selected' : '' }}>📦 Listo para recoger</option>
                                <option value="entregado" {{ $venta->estado_pedido == 'entregado' ? 'selected' : '' }}>🎉 Entregado</option>
                                <option value="cancelado" {{ $venta->estado_pedido == 'cancelado' ? 'selected' : '' }}>❌ Cancelado</option>
                            </select>
                            
                            @if($venta->estado_pedido != 'listo_para_recoger')
                            <button class="btn btn-success btn-listo" data-id="{{ $venta->id_venta }}">
                                <i class="fas fa-check-circle"></i> Listo para recoger
                            </button>
                            @else
                            <span style="color: #4CAF50;"><i class="fas fa-check"></i> Notificación enviada al cliente</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div style="margin-top: 20px;">
                {{ $ventas->appends(['token' => $token, 'estado' => $estado])->links() }}
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const token = '{{ $token }}';
        const csrf = '{{ csrf_token() }}';
        
        // Cambiar estado del pedido
        $('.estado-select').change(function() {
            const id = $(this).data('id');
            const estado = $(this).val();
            
            $.ajax({
                url: `/admin/ventas/${id}/estado-pedido?token=${token}`,
                method: 'PUT',
                data: { estado_pedido: estado, _token: csrf, token: token },
                success: function(res) {
                    if(res.success) location.reload();
                    else alert('Error: ' + res.message);
                },
                error: function() { alert('Error al actualizar estado'); }
            });
        });
        
        // Marcar como listo para recoger
        $('.btn-listo').click(function() {
            const id = $(this).data('id');
            if(!confirm('¿Marcar este pedido como "Listo para recoger"? El cliente recibirá una notificación por email.')) return;
            
            $.ajax({
                url: `/admin/ventas/${id}/listo-recoger?token=${token}`,
                method: 'POST',
                data: { _token: csrf, token: token },
                success: function(res) {
                    if(res.success) location.reload();
                    else alert('Error: ' + res.message);
                },
                error: function() { alert('Error al marcar pedido'); }
            });
        });
    </script>
</body>
</html>