<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pedido listo - Pet Spa</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; margin: 0; }
        .container { max-width: 550px; margin: auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #4CAF50, #45a049); color: white; padding: 25px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .success-icon { font-size: 60px; text-align: center; margin-bottom: 20px; }
        .btn { background: #4CAF50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 30px; display: inline-block; margin-top: 15px; }
        .info-card { background: #e8f5e9; padding: 15px; border-radius: 12px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; border-top: 1px solid #eee; }
        .info-row { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐾 Pet Spa</h1>
            <p>Tu pedido está listo</p>
        </div>
        <div class="content">
            <div class="success-icon">✅📦</div>
            <h2 style="text-align: center;">¡Tu pedido está listo!</h2>
            
            <p>Hola <strong>{{ $cliente->usuario->nombres }}</strong>,</p>
            <p>Tu pedido ya está listo para ser recogido en nuestra tienda.</p>
            
            <div class="info-card">
                <div class="info-row"><strong>📄 N° Pedido:</strong> #{{ $venta->id_venta }}</div>
                <div class="info-row"><strong>📅 Fecha:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
                <div class="info-row"><strong>💰 Total:</strong> Bs {{ number_format($venta->total, 2) }}</div>
            </div>
            
            <div class="info-card">
                <p><strong>📍 Dirección:</strong> Calle Principal #123, Ciudad</p>
                <p><strong>🕐 Horario:</strong> Lunes a Sábado 9:00 - 18:00 hrs</p>
                <p><strong>📞 Contacto:</strong> (591) 2-123456</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/cliente/mis-compras') }}" class="btn">Ver mis pedidos</a>
            </div>
        </div>
        <div class="footer">
            <p>Pet Spa - Cuidado y bienestar para tu mascota</p>
            <p>📧 contacto@petspa.com | 📞 (591) 2-123456</p>
        </div>
    </div>
</body>
</html>