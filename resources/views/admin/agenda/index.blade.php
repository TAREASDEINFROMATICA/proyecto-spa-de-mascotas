<!DOCTYPE html>
<html>
<head>
    <title>Agenda - Pet Spa</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .calendario { display: flex; overflow-x: auto; }
        .columna-groomer { min-width: 250px; border: 1px solid #ddd; margin: 5px; }
        .groomer-header { background: #4CAF50; color: white; padding: 10px; text-align: center; }
        .cita { background: #e3f2fd; margin: 5px; padding: 8px; border-radius: 5px; }
        .cita:hover { background: #bbdefb; }
        .btn { background: #4CAF50; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn-edit { background: #2196F3; font-size: 10px; padding: 2px 5px; text-decoration: none; color: white; border-radius: 3px; }
        .btn-delete { background: #f44336; font-size: 10px; padding: 2px 5px; text-decoration: none; color: white; border-radius: 3px; cursor: pointer; }
        .btn-back { background: #607d8b; }
        .fecha-nav { margin-bottom: 20px; }
        
        /* Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 10% auto; padding: 20px; border-radius: 10px; width: 400px; }
        .modal-content select, .modal-content textarea { width: 100%; padding: 8px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        .modal-content button { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-confirm { background: #f44336; color: white; }
        .btn-cancel { background: #607d8b; color: white; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📅 Agenda Maestra</h1>
        
        @php $token = request()->query('token'); @endphp
        
        <div class="fecha-nav">
            <a href="/admin/citas/create?token={{ $token }}" class="btn" style="background: #2196F3;">+ Nueva Cita</a>
            <br><br>
            <a href="?fecha={{ Carbon\Carbon::parse($fecha)->subDay()->format('Y-m-d') }}&token={{ $token }}" class="btn">◀ Día anterior</a>
            <strong style="margin: 0 15px;">{{ $fechaObj->format('d/m/Y') }} ({{ $fechaObj->translatedFormat('l') }})</strong>
            <a href="?fecha={{ Carbon\Carbon::parse($fecha)->addDay()->format('Y-m-d') }}&token={{ $token }}" class="btn">Día siguiente ▶</a>
        </div>
        
        <div class="calendario">
            @foreach($groomers as $groomer)
            <div class="columna-groomer">
                <div class="groomer-header">
                    ✂️ {{ $groomer->usuario->nombres }} {{ $groomer->usuario->apellidos }}
                </div>
                <div class="citas">
                    @foreach($citas->where('id_empleado', $groomer->id_empleado) as $cita)
                    @if($cita->estado == 'programado')
                    <div class="cita">
                        <strong>{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</strong><br>
                        🐕 {{ $cita->mascota->nombre }}<br>
                        ✂️ {{ $cita->servicio->nombre }}
                        <br>
                        <a href="/admin/citas/{{ $cita->id_cita }}/edit?token={{ $token }}" class="btn-edit">✏️ Editar</a>
                        <a href="#" onclick="mostrarMotivoCancelacion('{{ $cita->id_cita }}', '{{ $token }}')" class="btn-delete">❌ Cancelar</a>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        
        <br>
        @if($rol == 'admin')
    <a href="/admin/dashboard?token={{ $token }}" class="btn btn-back">← Volver al Dashboard de Admin</a>
@else
    <a href="/recepcion/dashboard?token={{ $token }}" class="btn btn-back">← Volver al Dashboard de Recepción</a>
@endif
    </div>

    <!-- Modal para cancelar cita -->
    <div id="modalCancelar" class="modal">
        <div class="modal-content">
            <h3>❌ Cancelar Cita</h3>
            <label>Motivo de cancelación:</label>
            <select id="motivo_cancelacion">
                <option value="Salud">Salud (mascota o dueño)</option>
                <option value="Emergencia">Emergencia</option>
                <option value="Cambio de horario">Cambio de horario</option>
                <option value="Cliente no asistió">Cliente no asistió</option>
                <option value="Otro">Otro</option>
            </select>
            
            <label>Observaciones (opcional):</label>
            <textarea id="observaciones_cancelacion" placeholder="Escribe aquí..."></textarea>
            
            <button onclick="confirmarCancelacion()" class="btn-confirm">✅ Confirmar Cancelación</button>
            <button onclick="cerrarModalCancelar()" class="btn-cancel">Cancelar</button>
        </div>
    </div>

    <script>
        let citaIdCancelar = null;
        let tokenCancelar = null;
        
        function mostrarMotivoCancelacion(citaId, token) {
            citaIdCancelar = citaId;
            tokenCancelar = token;
            document.getElementById('modalCancelar').style.display = 'block';
        }
        
        function cerrarModalCancelar() {
            document.getElementById('modalCancelar').style.display = 'none';
            citaIdCancelar = null;
            document.getElementById('motivo_cancelacion').value = 'Salud';
            document.getElementById('observaciones_cancelacion').value = '';
        }
        
        function confirmarCancelacion() {
            const motivo = document.getElementById('motivo_cancelacion').value;
            const observaciones = document.getElementById('observaciones_cancelacion').value;
            
            fetch('/admin/citas/' + citaIdCancelar + '/cancel?token=' + tokenCancelar, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    motivo: motivo,
                    observaciones: observaciones
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Cita cancelada correctamente');
                    location.reload();
                } else {
                    alert('❌ Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('❌ Error al cancelar la cita');
            });
        }
    </script>
</body>
</html>