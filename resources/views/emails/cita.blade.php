<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Notificación de Cita</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; margin: 0; }
        .container { max-width: 550px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 25px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .title { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 20px; text-align: center; }
        .info-card { background: #f8f9fa; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .info-row { margin-bottom: 12px; }
        .info-label { font-weight: bold; color: #555; display: inline-block; width: 100px; }
        .info-value { color: #333; }
        .btn { display: inline-block; background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 12px 25px; text-decoration: none; border-radius: 30px; margin-top: 15px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; border-top: 1px solid #eee; }
        .warning { background: #fff3cd; color: #856404; padding: 12px; border-radius: 10px; margin-top: 15px; text-align: center; }
        @media (max-width: 600px) { .content { padding: 20px; } .info-label { width: 100%; display: block; margin-bottom: 5px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐾 Pet Spa</h1>
            <p style="margin: 5px 0 0;">Cuidado y bienestar para tu mascota</p>
        </div>
        
        <div class="content">
            @if($tipo == 'solicitud')
                <div class="title">📋 Solicitud de Cita Recibida</div>
                <p>Hola <strong>{{ $cita->mascota->cliente->usuario->nombres ?? 'Cliente' }}</strong>,</p>
                <p>Hemos recibido tu solicitud de cita. Estaremos revisándola y te notificaremos cuando sea confirmada.</p>
                
            @elseif($tipo == 'confirmacion')
                <div class="title">✅ Cita Confirmada</div>
                <p>Hola <strong>{{ $cita->mascota->cliente->usuario->nombres ?? 'Cliente' }}</strong>,</p>
                <p>¡Tu cita ha sido <strong style="color: #4CAF50;">confirmada</strong>! Aquí tienes los detalles:</p>
                
            @else
                <div class="title">⏰ Recordatorio de Cita</div>
                <p>Hola <strong>{{ $cita->mascota->cliente->usuario->nombres ?? 'Cliente' }}</strong>,</p>
                <p>Te recordamos que tienes una cita <strong style="color: #ff9800;">en 1 hora</strong>.</p>
            @endif
            
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">📅 Fecha:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">⏰ Hora:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($cita->hora_fin)->format('H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🐕 Mascota:</span>
                    <span class="info-value">{{ $cita->mascota->nombre }} ({{ $cita->mascota->especie }})</span>
                </div>
                <div class="info-row">
                    <span class="info-label">✂️ Servicio:</span>
                    <span class="info-value">{{ $cita->servicio->nombre }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">✂️ Groomer:</span>
                    <span class="info-value">{{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}</span>
                </div>
            </div>
            
            @if($tipo == 'recordatorio')
            <div class="warning">
                ⚠️ Por favor, llega con 10 minutos de anticipación. Si no puedes asistir, contacta con nosotros.
            </div>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ url('/cliente/mis-citas') }}" class="btn">Ver mis citas</a>
            </div>
        </div>
        
        <div class="footer">
            <p>Pet Spa - Cuidado y bienestar para tu mascota</p>
            <p>📞 (591) 2-123456 | 📧 contacto@petspa.com</p>
        </div>
    </div>
</body>
</html>