<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mis Servicios</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h1 { color: #4CAF50; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #4CAF50; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <h1>📋 Mis Servicios Realizados</h1>
    <p><strong>Groomer:</strong> {{ $empleado->usuario->nombres ?? 'N/A' }} {{ $empleado->usuario->apellidos ?? '' }}</p>
    <p><strong>Fecha:</strong> {{ date('d/m/Y') }}</p>
    <p><strong>Total servicios:</strong> {{ $citas->count() }}</p>

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
                <td>{{ $cita->fecha }}</td>
                <td>{{ $cita->mascota->nombre ?? 'N/A' }}</td>
                <td>{{ $cita->servicio->nombre ?? 'N/A' }}</td>
                <td>{{ $cita->calificacion->puntuacion ?? 'Sin calificar' }}/5</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Reporte generado por el Sistema de Gestión de Spa de Mascotas
    </div>
</body>
</html>