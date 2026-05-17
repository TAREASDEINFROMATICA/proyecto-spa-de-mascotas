<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mis Servicios - {{ $empleado->usuario->nombres ?? 'Groomer' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #4CAF50;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
        .servicio-item {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Reporte de Servicios Realizados</h1>
        <p><strong>Groomer:</strong> {{ $empleado->usuario->nombres ?? 'N/A' }} {{ $empleado->usuario->apellidos ?? '' }}</p>
        <p><strong>Fecha de emisión:</strong> {{ date('d/m/Y H:i:s') }}</p>
        <p><strong>Total de servicios:</strong> {{ $citas->count() }}</p>
    </div>

    @if($citas->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Mascota</th>
                    <th>Servicio</th>
                    <th>Calificación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($citas as $index => $cita)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $cita->mascota->nombre ?? 'N/A' }}</td>
                    <td>{{ $cita->servicio->nombre ?? 'N/A' }}</td>
                    <td>
                        @if($cita->calificacion)
                            {{ $cita->calificacion->puntuacion }}/5 ⭐
                        @else
                            Sin calificar
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="total">
            Total de servicios: {{ $citas->count() }}
        </div>
    @else
        <p style="text-align: center; color: #999;">No hay servicios registrados</p>
    @endif

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema de Gestión de Spa de Mascotas</p>
    </div>
</body>
</html>