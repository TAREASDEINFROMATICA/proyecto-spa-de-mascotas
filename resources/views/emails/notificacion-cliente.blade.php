<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Notificación - Pet Spa</title>
</head>
<body>
    <h2>🐾 Pet Spa</h2>
    <p>Hola <strong>{{ $cita->mascota->cliente->usuario->nombres ?? 'Cliente' }}</strong>,</p>
    <p>{{ $mensaje }}</p>
    <hr>
    <p>📅 Fecha: {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</p>
    <p>🐕 Mascota: {{ $cita->mascota->nombre }}</p>
    <p>✂️ Servicio: {{ $cita->servicio->nombre }}</p>
    <br>
    <a href="{{ url('/cliente/mis-citas') }}">Ver mis citas</a>
</body>
</html>