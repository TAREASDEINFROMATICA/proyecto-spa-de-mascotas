<!DOCTYPE html>
<html>
<head>
    <title>Mis Calificaciones - Groomer</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .content { padding: 30px; }
        .promedio-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 16px; text-align: center; margin-bottom: 30px; }
        .promedio-card .valor { font-size: 48px; font-weight: 700; }
        .promedio-card .estrellas { font-size: 24px; margin-top: 10px; }
        .calificacion-card { background: #f8f9fa; border-radius: 16px; padding: 20px; margin-bottom: 15px; border-left: 4px solid #4CAF50; }
        .calificacion-card .mascota { font-weight: 600; color: #333; }
        .calificacion-card .servicio { color: #666; font-size: 14px; margin: 5px 0; }
        .calificacion-card .comentario { margin: 10px 0; color: #555; font-style: italic; }
        .calificacion-card .fecha { font-size: 12px; color: #999; }
        .estrellas { color: #ffc107; }
        .btn { background: #607d8b; color: white; padding: 10px 20px; border: none; border-radius: 12px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-star"></i> Mis Calificaciones</h1>
            <p>Lo que opinan los clientes de tu trabajo</p>
        </div>
        
        <div class="content">
            <div class="promedio-card">
                <div class="valor">{{ number_format($promedio, 1) }} / 5.0</div>
                <div class="estrellas">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= round($promedio) ? '' : 'far' }}"></i>
                    @endfor
                </div>
                <div style="margin-top: 10px;">Basado en {{ $total }} calificaciones</div>
            </div>
            
            @foreach($calificaciones as $calif)
            <div class="calificacion-card">
                <div class="mascota">
                    <i class="fas fa-dog"></i> {{ $calif->cita->mascota->nombre ?? 'Mascota' }}
                </div>
                <div class="servicio">
                    <i class="fas fa-cut"></i> {{ $calif->cita->servicio->nombre ?? 'Servicio' }}
                </div>
                <div class="estrellas">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $calif->puntuacion ? '' : 'far' }}"></i>
                    @endfor
                </div>
                <div class="comentario">
                    <i class="fas fa-quote-left"></i> {{ $calif->comentario ?? 'Sin comentario' }}
                </div>
                <div class="fecha">
                    <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($calif->fecha_calificacion)->format('d/m/Y') }}
                </div>
            </div>
            @endforeach
            
            @if($calificaciones->count() == 0)
                <div style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-star" style="font-size: 48px;"></i>
                    <p>No tienes calificaciones aún</p>
                    <p>Cuando los clientes califiquen tus servicios, aparecerán aquí</p>
                </div>
            @endif
            
            <div style="margin-top: 20px;">
                <a href="/groomer/dashboard?token={{ $token }}" class="btn">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
            
            <div class="pagination">
                {{ $calificaciones->appends(['token' => $token])->links() }}
            </div>
        </div>
    </div>
</body>
</html>