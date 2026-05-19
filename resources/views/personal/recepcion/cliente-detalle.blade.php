<!DOCTYPE html>
<html>
<head>
    <title>Detalle Cliente - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 50px 20px; 
        }
        .container { 
            max-width: 650px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 28px; 
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); 
            overflow: hidden; 
        }
        
        /* Header */
        .page-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 25px 30px;
        }
        .page-header h1 {
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-header h1 i { font-size: 28px; color: #ffd700; }
        
        /* Contenido */
        .content { padding: 30px; }
        
        /* Tarjeta de información */
        .info-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            width: 130px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-label i {
            width: 20px;
            color: #4CAF50;
        }
        .info-value {
            flex: 1;
            color: #334155;
            font-weight: 500;
        }
        
        /* Badge de estado */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-active {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: #2e7d32;
        }
        .badge-inactive {
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            color: #c62828;
        }
        
        /* Mascotas */
        .mascotas-section h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .mascotas-section h2 i { color: #ff9800; }
        
        .mascotas-list {
            list-style: none;
            background: #f8fafc;
            border-radius: 16px;
            padding: 15px;
            margin-top: 10px;
        }
        .mascotas-list li {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .mascotas-list li:last-child {
            border-bottom: none;
        }
        .mascotas-list li i {
            font-size: 20px;
            color: #ff9800;
            width: 30px;
        }
        .mascota-nombre {
            font-weight: 700;
            color: #1e293b;
        }
        .mascota-especie {
            color: #64748b;
            font-size: 13px;
        }
        .empty-mascotas {
            text-align: center;
            padding: 30px;
            color: #94a3b8;
            background: #f8fafc;
            border-radius: 16px;
        }
        .empty-mascotas i {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        
        /* Botones */
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 14px;
        }
        .btn-back {
            background: linear-gradient(135deg, #607d8b 0%, #455a64 100%);
            color: white;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-mascotas {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
        }
        .btn-mascotas:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76,175,80,0.3);
        }
        
        @media (max-width: 600px) {
            .container { margin: 0 10px; border-radius: 20px; }
            .content { padding: 20px; }
            .info-row { flex-direction: column; gap: 5px; }
            .info-label { width: 100%; }
            .button-group { flex-direction: column; }
            .btn { justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>
                <i class="fas fa-user-circle"></i>
                Detalle del Cliente
            </h1>
        </div>
        
        <div class="content">
            @php $token = request()->query('token'); @endphp
            
            <div class="info-card">
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-user"></i> Nombre:</div>
                    <div class="info-value">{{ $cliente->nombres }} {{ $cliente->apellidos }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-envelope"></i> Email:</div>
                    <div class="info-value">{{ $cliente->correo }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-phone"></i> Teléfono:</div>
                    <div class="info-value">{{ $cliente->telefono ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-id-card"></i> CI:</div>
                    <div class="info-value">{{ $cliente->ci ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-map-marker-alt"></i> Dirección:</div>
                    <div class="info-value">{{ $cliente->cliente->direccion ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-circle"></i> Estado:</div>
                    <div class="info-value">
                        @if($cliente->estado == 'activo')
                            <span class="badge badge-active">
                                <i class="fas fa-check-circle"></i> Activo
                            </span>
                        @else
                            <span class="badge badge-inactive">
                                <i class="fas fa-times-circle"></i> Inactivo
                            </span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-calendar-alt"></i> Registro:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($cliente->fecha_registro)->format('d/m/Y H:i') }}</div>
                </div>
            </div>
            
            <div class="mascotas-section">
                <h2><i class="fas fa-dog"></i> Mascotas del Cliente</h2>
                @if($mascotas->count() > 0)
                    <ul class="mascotas-list">
                        @foreach($mascotas as $mascota)
                        <li>
                            <i class="fas fa-paw"></i>
                            <span class="mascota-nombre">{{ $mascota->nombre }}</span>
                            <span class="mascota-especie">({{ $mascota->especie }}{{ $mascota->raza ? ' - ' . $mascota->raza : '' }})</span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-mascotas">
                        <i class="fas fa-dog"></i>
                        <p>No tiene mascotas registradas.</p>
                    </div>
                @endif
            </div>
            
            <div class="button-group">
                <a href="/recepcion/clientes?token={{ $token }}" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Volver a Clientes
                </a>
                <a href="/recepcion/mascotas?token={{ $token }}" class="btn btn-mascotas">
                    <i class="fas fa-dog"></i> Ver todas las mascotas
                </a>
            </div>
        </div>
    </div>
</body>
</html>