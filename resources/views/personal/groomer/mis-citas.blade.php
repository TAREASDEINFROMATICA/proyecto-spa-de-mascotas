<!DOCTYPE html>
<html>
<head>
    <title>Mis Citas - Groomer</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #4CAF50; color: white; }
        .btn { background: #607d8b; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
        .btn-ficha { background: #2196F3; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px; display: inline-block; }
        .btn-ficha:hover { background: #0b7dda; }
        .btn-ficha-ver { background: #4CAF50; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px; display: inline-block; }
        .btn-ficha-ver:hover { background: #45a049; }
        .estado-programado { color: green; font-weight: bold; }
        .estado-reservado { color: orange; font-weight: bold; }
        .estado-concluido { color: #2196F3; font-weight: bold; }
        .estado-cancelado { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📅 Mis Citas</h1>
        
        @php $token = request()->query('token'); @endphp
        
        @if($citas->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Mascota</th>
                    <th>Servicio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($citas as $cita)
                <tr>
                    <td>{{ $cita->fecha }}</td>
                    <td>{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</td>
                    <td>{{ $cita->mascota->nombre }}</td>
                    <td>{{ $cita->servicio->nombre }}</td>
                    <td class="estado-{{ $cita->estado }}">
                        @if($cita->estado == 'programado')
                            ✅ Programado
                        @elseif($cita->estado == 'reservado')
                            ⏳ Pendiente
                        @elseif($cita->estado == 'concluido')
                            🏁 Concluido
                        @else
                            ❌ Cancelado
                        @endif
                    </td>
                    <td>
                        @if($cita->estado == 'concluido')
                            <a href="#" class="btn-ficha-ver" data-cita-id="{{ $cita->id_cita }}">👁️ Ver Ficha (solo lectura)</a>
                        @else
                            <a href="#" class="btn-ficha" data-cita-id="{{ $cita->id_cita }}">📋 Ver Ficha</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p>No tienes citas registradas.</p>
        @endif
        
        <a href="/groomer/dashboard?token={{ $token }}" class="btn">← Volver al Dashboard</a>
    </div>

    <script>
        const token = '{{ $token }}';
        
        // Para citas no concluidas (editable)
        document.querySelectorAll('.btn-ficha').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const citaId = this.getAttribute('data-cita-id');
                window.location.href = '/groomer/ficha-tecnica/' + citaId + '?token=' + token;
            });
        });
        
        // Para citas concluidas (solo lectura)
        document.querySelectorAll('.btn-ficha-ver').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const citaId = this.getAttribute('data-cita-id');
                window.location.href = '/groomer/ficha-tecnica-ver/' + citaId + '?token=' + token;
            });
        });
    </script>
</body>
</html>