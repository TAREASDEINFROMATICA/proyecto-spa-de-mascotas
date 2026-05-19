<!DOCTYPE html>
<html>
<head>
    <title>Clientes - Pet Spa</title>
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
            max-width: 1400px; 
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
        
        /* Tabla */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #e0e0e0;
            background: white;
            margin-top: 20px;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #f8fafc;
            padding: 16px 12px;
            text-align: left;
            font-weight: 700;
            font-size: 13px;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: 14px 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
            font-size: 13px;
            color: #334155;
        }
        tr:hover td {
            background: #faf5ff;
        }
        
        /* Badges de estado */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
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
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-ver { background: #4CAF50; color: white; }
        .btn-ver:hover { background: #45a049; transform: scale(1.02); }
        .btn-mascotas { background: #2196F3; color: white; }
        .btn-mascotas:hover { background: #1976D2; transform: scale(1.02); }
        
        /* Botón volver */
        .back-link {
            margin-bottom: 20px;
            display: inline-block;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #607d8b 0%, #455a64 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 14px;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Acciones en tabla */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .content { padding: 20px; }
            th, td { padding: 8px; font-size: 11px; }
            .btn { padding: 4px 8px; font-size: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>
                <i class="fas fa-users"></i>
                Gestión de Clientes
            </h1>
            <p><i class="fas fa-user-friends"></i> Lista de todos los clientes registrados</p>
        </div>
        
        <div class="content">
            @php $token = request()->query('token'); @endphp
            
            <div class="back-link">
                @if(isset($rol) && $rol == 'admin')
                    <a href="/admin/dashboard?token={{ $token }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard de Admin
                    </a>
                @else
                    <a href="/recepcion/dashboard?token={{ $token }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard de Recepción
                    </a>
                @endif
            </div>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                        <tr>
                            <td><strong>#{{ $cliente->id_usuario }}</strong></td>
                            <td>{{ $cliente->nombres }} {{ $cliente->apellidos }}</td>
                            <td>{{ $cliente->correo }}</td>
                            <td>{{ $cliente->telefono ?? '-' }}</td>
                            <td>
                                @if($cliente->estado == 'activo')
                                    <span class="badge badge-active">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </span>
                                @else
                                    <span class="badge badge-inactive">
                                        <i class="fas fa-times-circle"></i> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="/recepcion/clientes/{{ $cliente->id_usuario }}/ver?token={{ $token }}&rol={{ $rol ?? 'recepcion' }}" class="btn btn-ver">
                                        <i class="fas fa-eye"></i> Ver Detalle
                                    </a>
                                   
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>