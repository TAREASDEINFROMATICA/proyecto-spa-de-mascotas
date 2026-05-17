<!DOCTYPE html>
<html>
<head>
    <title>Detalle de Compra #{{ $venta->id_venta }} - Pet Spa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .btn-print { background: #607d8b; color: white; border: none; cursor: pointer; }
        .btn-print:hover { background: #546e7a; }
        .content { padding: 30px; }
        .info-card { background: #f8f9fa; border-radius: 16px; padding: 20px; margin-bottom: 20px; }
        .info-card h3 { margin-bottom: 15px; color: #333; display: flex; align-items: center; gap: 10px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .info-item { margin-bottom: 10px; }
        .info-label { font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase; }
        .info-value { font-size: 16px; color: #333; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #e9ecef; padding: 12px; text-align: left; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #dee2e6; }
        .total { text-align: right; font-size: 18px; margin-top: 15px; padding-top: 15px; border-top: 2px solid #e0e0e0; }
        .total span { font-size: 24px; color: #4CAF50; font-weight: 700; }
        .estado-pagada { background: #e8f5e9; color: #4CAF50; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .comprobante { background: #e3f2fd; padding: 15px; border-radius: 12px; text-align: center; margin-top: 20px; }
        .comprobante i { font-size: 40px; color: #2196F3; margin-bottom: 10px; }
        @media (max-width: 768px) { .header { flex-direction: column; text-align: center; } .info-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-receipt"></i> Detalle de Compra #{{ $venta->id_venta }}</h1>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()" class="btn btn-print">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <a href="/cliente/mis-compras?token={{ $token }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="content">
            <!-- Información de la compra -->
            <div class="info-card">
                <h3><i class="fas fa-info-circle"></i> Información de la Compra</h3>
                <div class="info-grid">
                    <div>
                        <div class="info-label">N° Venta</div>
                        <div class="info-value">#{{ $venta->id_venta }}</div>
                    </div>
                    <div>
                        <div class="info-label">Fecha</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i:s') }}</div>
                    </div>
                    <div>
                        <div class="info-label">Estado</div>
                        <div class="info-value"><span class="estado-pagada"><i class="fas fa-check-circle"></i> Pagada</span></div>
                    </div>
                    <div>
                        <div class="info-label">Comprobante</div>
                        <div class="info-value">{{ $venta->comprobante->numero_comprobante ?? 'No disponible' }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Productos -->
            <div class="info-card">
                <h3><i class="fas fa-boxes"></i> Productos</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre }}</td>
                            <td>Bs {{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>Bs {{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="total">
                    TOTAL: <span>Bs {{ number_format($venta->total, 2) }}</span>
                </div>
            </div>
            
            <!-- Información del pago -->
            <div class="info-card">
                <h3><i class="fas fa-credit-card"></i> Información del Pago</h3>
                <div class="info-grid">
                    <div>
                        <div class="info-label">Método de Pago</div>
                        <div class="info-value">{{ $venta->pagos->first()->metodoPago->nombre ?? 'No registrado' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Monto Pagado</div>
                        <div class="info-value">Bs {{ number_format($venta->pagos->first()->monto ?? $venta->total, 2) }}</div>
                    </div>
                    <div>
                        <div class="info-label">Fecha de Pago</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($venta->pagos->first()->fecha_pago ?? now())->format('d/m/Y H:i:s') }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Comprobante digital -->
            <div class="comprobante">
                <i class="fas fa-file-invoice"></i>
                <h3>Comprobante Digital</h3>
                <p>Este documento es un comprobante válido de tu compra.</p>
                <p style="margin-top: 10px; font-size: 12px; color: #666;">
                    Código: {{ $venta->comprobante->numero_comprobante ?? 'N/A' }}
                </p>
            </div>
            
            <!-- Mensaje de agradecimiento -->
            <div style="text-align: center; margin-top: 30px; padding: 20px; background: #e8f5e9; border-radius: 12px;">
                <i class="fas fa-heart" style="color: #4CAF50; font-size: 24px;"></i>
                <p style="margin-top: 10px;">¡Gracias por tu compra! Esperamos que tu mascota disfrute los productos.</p>
            </div>
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
    </script>
</body>
</html>