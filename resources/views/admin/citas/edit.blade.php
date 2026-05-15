<!DOCTYPE html>
<html>
<head>
    <title>Editar Cita - Pet Spa</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial; margin: 50px; background: #f5f5f5; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #2196F3; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        .error { color: red; margin-bottom: 15px; }
        .horarios { margin-top: 20px; }
        .slot { background: #e3f2fd; padding: 10px; margin: 5px; border-radius: 5px; cursor: pointer; display: inline-block; }
        .slot:hover { background: #bbdefb; }
        .slot-seleccionado { background: #4CAF50; color: white; }
        .info-actual { background: #e8f5e9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #4CAF50; }
        .info-actual strong { color: #2e7d32; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ Editar Cita</h2>
        
        @php $token = request()->query('token'); @endphp
        
        <!-- Pasar cita_id a JavaScript -->
        <div id="citaData" data-cita-id="{{ $cita->id_cita }}" style="display: none;"></div>
        
        <!-- Mostrar información actual de la cita -->
        <div class="info-actual">
            <strong>📋 Información actual de la cita:</strong><br>
            🐕 <strong>Mascota:</strong> {{ $cita->mascota->nombre }}<br>
            ✂️ <strong>Servicio:</strong> {{ $cita->servicio->nombre }}<br>
            👤 <strong>Groomer:</strong> {{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}<br>
            📅 <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}<br>
            🕐 <strong>Hora:</strong> {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}
        </div>
        
        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        
        <form id="citaForm" method="POST" action="/admin/citas/{{ $cita->id_cita }}?token={{ $token }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="token" value="{{ $token }}">
            
            <label>Mascota *</label>
            <select name="id_mascota" id="id_mascota" required>
                <option value="">Seleccionar mascota</option>
                @foreach($mascotas as $m)
                    <option value="{{ $m->id_mascota }}" {{ $cita->id_mascota == $m->id_mascota ? 'selected' : '' }}>
                        {{ $m->nombre }} ({{ $m->cliente->usuario->nombres }})
                    </option>
                @endforeach
            </select>
            
            <label>Servicio *</label>
            <select name="id_servicio" id="id_servicio" required>
                <option value="">Seleccionar servicio</option>
                @foreach($servicios as $s)
                    <option value="{{ $s->id_servicio }}" {{ $cita->id_servicio == $s->id_servicio ? 'selected' : '' }}>
                        {{ $s->nombre }} ({{ $s->duracion_minutos }} min - ${{ number_format($s->precio, 2) }})
                    </option>
                @endforeach
            </select>
            
            <label>Groomer *</label>
            <select name="id_empleado" id="id_empleado" required>
                <option value="">Seleccionar groomer</option>
                @foreach($groomers as $g)
                    <option value="{{ $g->id_empleado }}" {{ $cita->id_empleado == $g->id_empleado ? 'selected' : '' }}>
                        ✂️ {{ $g->usuario->nombres }} {{ $g->usuario->apellidos }}
                    </option>
                @endforeach
            </select>
            
            <label>Fecha *</label>
            <input type="date" name="fecha" id="fecha" value="{{ $cita->fecha }}" required>
            
            <div id="horariosContainer">
                <h3>📅 Horarios disponibles</h3>
                <div id="horariosLista" class="horarios">
                    <div style="color: orange;">Seleccione mascota, servicio, groomer y fecha para ver horarios</div>
                </div>
            </div>
            
            <div style="margin-top: 15px; padding: 10px; background: #f5f5f5; border-radius: 5px;">
                <strong>🕐 Hora seleccionada actualmente:</strong>
                <span id="horaSeleccionadaDisplay">{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</span>
            </div>
            
            <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ $cita->hora_inicio }}">
            <input type="hidden" name="hora_fin" id="hora_fin" value="{{ $cita->hora_fin }}">
            
            <button type="submit" id="submitBtn">💾 Guardar Cambios</button>
        </form>
        
        <br>
        <a href="/admin/agenda?fecha={{ $cita->fecha }}&token={{ $token }}">← Volver a la Agenda</a>
    </div>

    <script>
        // Obtener citaId desde el atributo data
        const citaId = document.getElementById('citaData').getAttribute('data-cita-id');
        
        function actualizarHoraDisplay(inicio, fin) {
            $('#horaSeleccionadaDisplay').text(inicio + ' - ' + fin);
        }
        
        function cargarHorarios() {
            const mascotaId = $('#id_mascota').val();
            const servicioId = $('#id_servicio').val();
            const empleadoId = $('#id_empleado').val();
            const fecha = $('#fecha').val();
            
            console.log('Mascota:', mascotaId, 'Servicio:', servicioId, 'Empleado:', empleadoId, 'Fecha:', fecha, 'CitaId:', citaId);
            
            if (!mascotaId || !servicioId || !empleadoId || !fecha) {
                $('#horariosLista').html('<div style="color: orange;">Complete todos los campos para ver horarios disponibles</div>');
                return;
            }
            
            $('#horariosLista').html('<div>Cargando horarios...</div>');
            
            $.ajax({
                url: '/admin/citas/horarios',
                method: 'GET',
                data: {
                    mascota_id: mascotaId,
                    servicio_id: servicioId,
                    empleado_id: empleadoId,
                    fecha: fecha,
                    cita_id: citaId,
                    token: '{{ $token }}'
                },
                success: function(data) {
                    console.log('Horarios recibidos:', data);
                    if (data.length === 0) {
                        $('#horariosLista').html('<div style="color: red;">No hay horarios disponibles para este día</div>');
                    } else {
                        let html = '';
                        const horaActualInicio = $('#hora_inicio').val();
                        data.forEach(slot => {
                            const seleccionado = (slot.hora_inicio === horaActualInicio);
                            html += `<div class="slot ${seleccionado ? 'slot-seleccionado' : ''}" 
                                        onclick="seleccionarHorario('${slot.hora_inicio}', '${slot.hora_fin}')">
                                        🕐 ${slot.hora_inicio} - ${slot.hora_fin} (${slot.duracion} min)
                                    </div>`;
                        });
                        $('#horariosLista').html(html);
                    }
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                    $('#horariosLista').html('<div style="color: red;">Error al cargar horarios</div>');
                }
            });
        }
        
        function seleccionarHorario(inicio, fin) {
            $('#hora_inicio').val(inicio);
            $('#hora_fin').val(fin);
            actualizarHoraDisplay(inicio, fin);
            $('.slot').removeClass('slot-seleccionado');
            $(event.target).addClass('slot-seleccionado');
        }
        
        $(document).ready(function() {
            $('#id_mascota, #id_servicio, #id_empleado, #fecha').on('change', cargarHorarios);
            cargarHorarios();
        });
    </script>
</body>
</html>