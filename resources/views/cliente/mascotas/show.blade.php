<!DOCTYPE html>
<html>
<head>
    <title>{{ $mascota->nombre }} - Pet Spa</title>
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
            max-width: 900px; 
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
            text-align: center;
        }
        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .page-header h1 i { font-size: 32px; color: #ffd700; }
        
        /* Contenido */
        .content { padding: 30px; }
        
        /* Foto */
        .foto-section {
            text-align: center;
            margin-bottom: 25px;
        }
        .foto-container {
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            display: inline-block;
            width: 100%;
            max-width: 300px;
        }
        .foto-container img {
            width: 100%;
            height: auto;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .foto-placeholder {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            border-radius: 16px;
            padding: 50px;
            text-align: center;
            color: #64748b;
            font-size: 48px;
        }
        
        /* Tarjeta de información */
        .info-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .info-item {
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-item:nth-last-child(1),
        .info-item:nth-last-child(2) {
            border-bottom: none;
        }
        .info-label {
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-label i {
            width: 20px;
            color: #4CAF50;
        }
        .info-value {
            color: #334155;
            font-weight: 500;
            font-size: 14px;
            padding-left: 28px;
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
        
        /* Botones */
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 10px;
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
        .btn-edit {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
        }
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33,150,243,0.3);
        }
        
        @media (max-width: 640px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .info-item {
                border-bottom: 1px solid #e2e8f0;
            }
            .info-item:last-child {
                border-bottom: none;
            }
            .info-value {
                padding-left: 28px;
            }
            .button-group {
                flex-direction: column;
            }
            .btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>
                <i class="fas fa-paw"></i>
                {{ $mascota->nombre }}
            </h1>
        </div>
        
        <div class="content">
            @php
                $token = request()->query('token');
            @endphp
            
            <div class="foto-section">
                @if($mascota->foto)
                    <div class="foto-container">
                        <img src="{{ Storage::url($mascota->foto) }}" alt="{{ $mascota->nombre }}">
                    </div>
                @else
                    <div class="foto-placeholder">
                        <i class="fas fa-dog"></i>
                        <p style="margin-top: 10px;">Sin foto</p>
                    </div>
                @endif
            </div>
            
            <div class="info-card">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-user"></i> Dueño</div>
                        <div class="info-value">{{ $mascota->cliente->usuario->nombres }} {{ $mascota->cliente->usuario->apellidos }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-paw"></i> Especie</div>
                        <div class="info-value">{{ $mascota->especie }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-dna"></i> Raza</div>
                        <div class="info-value">{{ $mascota->raza ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-venus-mars"></i> Sexo</div>
                        <div class="info-value">{{ $mascota->sexo ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-calendar"></i> Edad</div>
                        <div class="info-value">{{ $mascota->fecha_nacimiento ? \Carbon\Carbon::parse($mascota->fecha_nacimiento)->age . ' años' : '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-weight-hanging"></i> Peso</div>
                        <div class="info-value">{{ $mascota->peso ? $mascota->peso . ' kg' : '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-palette"></i> Color</div>
                        <div class="info-value">{{ $mascota->color ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-brain"></i> Temperamento</div>
                        <div class="info-value">{{ ucfirst($mascota->temperamento_general ?? '-') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-allergies"></i> Alergias</div>
                        <div class="info-value">{{ $mascota->alergias ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-heartbeat"></i> Cuidados especiales</div>
                        <div class="info-value">{{ $mascota->cuidados_especiales ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-sticky-note"></i> Observaciones</div>
                        <div class="info-value">{{ $mascota->observaciones ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-circle"></i> Estado</div>
                        <div class="info-value">
                            @if($mascota->estado == 'activa')
                                <span class="badge badge-active">
                                    <i class="fas fa-check-circle"></i> Activa
                                </span>
                            @else
                                <span class="badge badge-inactive">
                                    <i class="fas fa-times-circle"></i> Inactiva
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="button-group">
                @if(isset($rol) && $rol == 'admin')
                    <a href="/admin/mascotas?token={{ $token }}" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                @elseif(isset($rol) && $rol == 'recepcion')
                    <a href="/recepcion/mascotas?token={{ $token }}" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                @else
                    <a href="/cliente/mascotas?token={{ $token }}" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                @endif
                
                <a href="/cliente/mascotas/{{ $mascota->id_mascota }}/edit?token={{ $token }}" class="btn btn-edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>
    </div>
</body>
</html>