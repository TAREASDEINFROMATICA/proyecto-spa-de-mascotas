<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recordatorio de Cita</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #4CAF50; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #4CAF50; margin: 0; }
        .info { margin: 15px 0; }
        .info-label { font-weight: 600; color: #666; }
        .info-value { font-weight: 700; color: #333; }
        .btn { background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; display: inline-block; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐾 Pet Spa</h1>
            <p>Recordatorio de tu cita</p>
        </div>
        
        <p>Hola <strong>{{ $cita->mascota->cliente->usuario->nombres ?? 'Cliente' }}</strong>,</p>
        
        <p>Te recordamos que tienes una cita programada para tu mascota:</p>
        
        <div class="info">
            <div><span class="info-label">📅 Fecha:</span> <span class="info-value">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</span></div>
            <div><span class="info-label">⏰ Hora:</span> <span class="info-value">{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</span></div>
            <div><span class="info-label">🐕 Mascota:</span> <span class="info-value">{{ $cita->mascota->nombre }}</span></div>
            <div><span class="info-label">✂️ Servicio:</span> <span class="info-value">{{ $cita->servicio->nombre }}</span></div>
            <div><span class="info-label">✂️ Groomer:</span> <span class="info-value">{{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}</span></div>
        </div>
        
        <p>Por favor, llega con 10 minutos de anticipación. Si no puedes asistir, contacta con nosotros para reprogramar.</p>
        
        <div style="text-align: center;">
            <a href="{{ url('/cliente/mis-citas') }}" class="btn">Ver mis citas</a>
        </div>
        
        <div class="footer">
            <p>Pet Spa - Cuidado y bienestar para tu mascota</p>
            <p>Teléfono: (591) 2-123456</p>
        </div>
    </div>
</body>
</html>