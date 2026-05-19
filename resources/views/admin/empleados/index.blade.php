<!DOCTYPE html>
<html>
<head>
    <title>Empleados - Pet Spa</title>
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
        
        /* Contenedor principal */
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
        .alert-error { 
            background: linear-gradient(135deg, #f8d7da 0%, #ffebee 100%);
            color: #721c24; 
            border-left: 4px solid #dc3545;
        }
        
        /* Botón agregar */
        .btn-add {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            transition: all 0.3s;
            border: none;
            font-size: 14px;
            box-shadow: 0 4px 10px rgba(76,175,80,0.3);
        }
        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(76,175,80,0.4);
        }
        
        /* Tabla */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #e0e0e0;
            background: white;
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
        
        /* Botones de acción */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-icon {
            padding: 7px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-edit { background: #2196F3; color: white; }
        .btn-edit:hover { background: #1976D2; transform: scale(1.02); }
        .btn-delete { background: #f44336; color: white; }
        .btn-delete:hover { background: #d32f2f; transform: scale(1.02); }
        .btn-activate { background: #4CAF50; color: white; }
        .btn-activate:hover { background: #45a049; transform: scale(1.02); }
        
        /* Botón volver */
        .back-link {
            margin-top: 30px;
            text-align: center;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #607d8b 0%, #455a64 100%);
            color: white;
            padding: 12px 28px;
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
        
        /* Formularios inline */
        form { display: inline; }
        
        /* Responsive */
        @media (max-width: 768px) {
            body { padding: 20px 10px; }
            .content { padding: 20px; }
            th, td { padding: 10px 8px; font-size: 11px; }
            .btn-icon { padding: 4px 8px; font-size: 10px; }
            .page-header h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>
                <i class="fas fa-users"></i>
                Gestión de Empleados
            </h1>
            <p><i class="fas fa-briefcase"></i> Administra el personal del Spa de Mascotas</p>
        </div>
        
        <div class="content">
            @php $token = request()->query('token'); @endphp

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
            
            <a href="{{ route('empleados.create') }}?token={{ $token }}" class="btn-add">
                <i class="fas fa-plus-circle"></i> Nuevo Empleado
            </a>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>CI</th>
                            <th>Teléfono</th>
                            <th>Cargo</th>
                            <th>Especialidad</th>
                            <th>Turno</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($empleados as $empleado)
                        <tr>
                            <td><strong>#{{ $empleado->id_empleado }}</strong></td>
                            <td>{{ $empleado->usuario->nombres }} {{ $empleado->usuario->apellidos }}</td>
                            <td>{{ $empleado->usuario->correo }}</td>
                            <td>{{ $empleado->usuario->ci ?? '-' }}</td>
                            <td>{{ $empleado->usuario->telefono ?? '-' }}</td>
                            <td><span style="background: #e3f2fd; padding: 4px 10px; border-radius: 20px; font-size: 11px;">{{ $empleado->cargo }}</span></td>
                            <td>{{ $empleado->especialidad ?? '-' }}</td>
                            <td>{{ $empleado->turno ?? '-' }}</td>
                            <td>
                                @if($empleado->usuario->estado == 'activo')
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
                                    <a href="{{ route('empleados.edit', $empleado->id_empleado) }}?token={{ $token }}" class="btn-icon btn-edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    
                                    @if($empleado->usuario->estado == 'activo')
                                        <form action="{{ route('empleados.destroy', $empleado->id_empleado) }}?token={{ $token }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon btn-delete" onclick="return confirm('¿Desactivar este empleado?')">
                                                <i class="fas fa-ban"></i> Desactivar
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('empleados.activate', $empleado->id_empleado) }}?token={{ $token }}" method="POST" style="display:inline;">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn-icon btn-activate" onclick="return confirm('¿Activar este empleado?')">
                                                <i class="fas fa-check-circle"></i> Activar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="back-link">
                <a href="/admin/dashboard?token={{ $token }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>