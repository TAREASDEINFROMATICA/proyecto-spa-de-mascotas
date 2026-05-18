<!DOCTYPE html>
<html>
<head>
    <title>Mis Notificaciones - Pet Spa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .content { padding: 30px; }
        .notificacion-card { background: #f8f9fa; border-radius: 12px; padding: 15px; margin-bottom: 10px; border-left: 4px solid; transition: all 0.2s; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .notificacion-card.pendiente { border-left-color: #ff9800; background: #fff8f0; }
        .notificacion-card.leida { border-left-color: #4CAF50; background: #f8f9fa; opacity: 0.7; }
        .notificacion-mensaje { flex: 1; }
        .notificacion-fecha { font-size: 11px; color: #999; margin-top: 5px; }
        .empty-message { text-align: center; padding: 40px; color: #999; }
        .empty-message i { font-size: 48px; margin-bottom: 10px; display: block; }
        .badge { font-size: 10px; padding: 2px 8px; border-radius: 20px; }
        .badge-nueva { background: #ff9800; color: white; }
        .badge-leida { background: #4CAF50; color: white; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; }
        @media (max-width: 640px) { .notificacion-card { flex-direction: column; align-items: flex-start; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
    <h1><i class="fas fa-bell"></i> Mis Notificaciones</h1>
    @if($rol == 'admin')
        <a href="/admin/dashboard?token={{ $token }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    @elseif($rol == 'recepcion')
        <a href="/recepcion/dashboard?token={{ $token }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    @elseif($rol == 'groomer')
        <a href="/groomer/dashboard?token={{ $token }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    @else
        <a href="/cliente/dashboard?token={{ $token }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    @endif
</div>
        
        <div class="content">
            @if($notificaciones->count() > 0)
                @foreach($notificaciones as $notif)
                <div class="notificacion-card {{ $notif->estado }}">
                    <div class="notificacion-mensaje">
                        <strong>
                            @if($notif->tipo == 'cita_recordatorio') 📅 Recordatorio de cita
                            @elseif($notif->tipo == 'cita_confirmada') ✅ Cita confirmada
                            @elseif($notif->tipo == 'cita_cancelada') ❌ Cita cancelada
                            @elseif($notif->tipo == 'servicio_cerrado') 🏁 Servicio finalizado
                            @else ℹ️ Información
                            @endif
                        </strong>
                        <p style="margin-top: 5px;">{{ $notif->mensaje }}</p>
                        <div class="notificacion-fecha">
                            <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($notif->fecha_envio)->diffForHumans() }}
                        </div>
                    </div>
                    <div>
                        @if($notif->estado == 'pendiente')
                            <span class="badge badge-nueva">Nueva</span>
                        @else
                            <span class="badge badge-leida">Leída</span>
                        @endif
                    </div>
                </div>
                @endforeach
                
                <div class="pagination">
                    {{ $notificaciones->appends(['token' => $token])->links() }}
                </div>
            @else
                <div class="empty-message">
                    <i class="fas fa-bell-slash"></i>
                    <p>No tienes notificaciones</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>