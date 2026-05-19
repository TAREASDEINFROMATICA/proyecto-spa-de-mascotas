<!DOCTYPE html>
<html>
<head>
    <title>Mis Mascotas - Pet Spa</title>
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
            padding: 40px 20px; 
        }
        .container { 
            max-width: 1200px; 
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
            padding: 30px 35px;
        }
        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .page-header h1 i { font-size: 32px; color: #ffd700; }
        .page-header p {
            margin-top: 8px;
            opacity: 0.8;
            font-size: 14px;
        }
        
        /* Contenido */
        .content { padding: 30px 35px; }
        
        /* Alertas */
        .alert {
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-success { 
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724; 
            border-left: 4px solid #28a745;
        }
        
        /* Botones superiores */
        .header-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 14px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76,175,80,0.3);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #607d8b 0%, #455a64 100%);
            color: white;
        }
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Grid de mascotas */
        .mascotas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 10px;
        }
        
        /* Tarjeta de mascota */
        .mascota-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }
        .mascota-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .mascota-imagen {
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .mascota-imagen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .mascota-card:hover .mascota-imagen img {
            transform: scale(1.05);
        }
        .mascota-imagen-placeholder {
            font-size: 64px;
            color: #94a3b8;
        }
        
        .mascota-info {
            padding: 20px;
        }
        .mascota-nombre {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mascota-nombre i {
            color: #ff9800;
        }
        .mascota-detalle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mascota-detalle i {
            width: 20px;
            color: #4CAF50;
        }
        
        .card-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        .btn-card {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-ficha {
            background: #4CAF50;
            color: white;
        }
        .btn-ficha:hover {
            background: #45a049;
            transform: scale(1.02);
        }
        .btn-edit {
            background: #2196F3;
            color: white;
        }
        .btn-edit:hover {
            background: #1976D2;
            transform: scale(1.02);
        }
        
        /* Sin mascotas */
        .empty-state {
            text-align: center;
            padding: 60px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 24px;
            margin-top: 20px;
        }
        .empty-state i {
            font-size: 64px;
            color: #94a3b8;
            margin-bottom: 15px;
            display: block;
        }
        .empty-state p {
            color: #64748b;
            margin-bottom: 20px;
        }
        
        @media (max-width: 640px) {
            .content { padding: 20px; }
            .mascotas-grid { grid-template-columns: 1fr; }
            .header-buttons { flex-direction: column; }
            .btn { justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>
                <i class="fas fa-dog"></i>
                Mis Mascotas
            </h1>
            <p><i class="fas fa-paw"></i> Gestiona la información de tus mascotas</p>
        </div>
        
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            @php
                $token = request()->query('token');
            @endphp
            
            <div class="header-buttons">
                <a href="/cliente/mascotas/create?token={{ $token }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Agregar Mascota
                </a>
                <a href="/cliente/dashboard?token={{ $token }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
            
            @if(count($mascotas) > 0)
                <div class="mascotas-grid">
                    @foreach($mascotas as $mascota)
                    <div class="mascota-card">
                        <div class="mascota-imagen">
                            @if($mascota->foto)
                                <img src="{{ Storage::url($mascota->foto) }}" alt="{{ $mascota->nombre }}">
                            @else
                                <div class="mascota-imagen-placeholder">
                                    <i class="fas fa-paw"></i>
                                </div>
                            @endif
                        </div>
                        <div class="mascota-info">
                            <div class="mascota-nombre">
                                <i class="fas fa-tag"></i> {{ $mascota->nombre }}
                            </div>
                            <div class="mascota-detalle">
                                <i class="fas fa-paw"></i> {{ $mascota->especie }}
                            </div>
                            <div class="mascota-detalle">
                                <i class="fas fa-dna"></i> {{ $mascota->raza ?? 'Sin raza' }}
                            </div>
                            <div class="card-buttons">
                                <a href="/cliente/mascotas/{{ $mascota->id_mascota }}?token={{ $token }}" class="btn-card btn-ficha">
                                    <i class="fas fa-info-circle"></i> Ficha
                                </a>
                                <a href="/cliente/mascotas/{{ $mascota->id_mascota }}/edit?token={{ $token }}" class="btn-card btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-paw"></i>
                    <p>🐾 No tienes mascotas registradas aún.</p>
                    <a href="/cliente/mascotas/create?token={{ $token }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Registrar mi primera mascota
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>