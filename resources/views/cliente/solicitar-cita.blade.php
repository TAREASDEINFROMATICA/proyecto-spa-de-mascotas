<!DOCTYPE html>
<html>
<head>
    <title>Solicitar Cita - Pet Spa</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial; margin: 50px; background: #fff3e0; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        .error { color: red; margin-bottom: 15px; }
        .horarios { margin-top: 20px; }
        .slot { background: #e3f2fd; padding: 10px; margin: 5px; border-radius: 5px; cursor: pointer; display: inline-block; }
        .slot:hover { background: #bbdefb; }
        .slot-seleccionado { background: #4CAF50; color: white; }
        .info { background: #e3f2fd; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📅 Solicitar Cita</h2>
        
        <div class="info">
            💡 Tu solicitud quedará en estado <strong>"Pendiente"</strong> hasta que recepción la confirme.
        </div>
        
        @php $token = request()->query('token'); @endphp
        
        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        
        <form id="citaForm" method="POST" action="/cliente/solicitar-cita?token={{ $token }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <label>Mascota *</label>
            <select name="id_mascota" id="id_mascota" required>
                <option value="">Seleccionar mascota</option>
                @foreach($mascotas as $m)
                    <option value="{{ $m->id_mascota }}">{{ $m->nombre }} ({{ $m->especie }})</option>
                @endforeach
            </select>
            
            <label>Servicio *</label>
            <select name="id_servicio" id="id_servicio" required>
                <option value="">Seleccionar servicio</option>
                @foreach($servicios as $s)
                    <option value="{{ $s->id_servicio }}">{{ $s->nombre }} ({{ $s->duracion_minutos }} min - ${{ number_format($s->precio, 2) }})</option>
                @endforeach
            </select>
            
            <label>Groomer *</label>
            <select name="id_empleado" id="id_empleado" required>
                <option value="">Seleccionar groomer</option>
                @foreach($groomers as $g)
                    <option value="{{ $g->id_empleado }}">✂️ {{ $g->usuario->nombres }} {{ $g->usuario->apellidos }}</option>
                @endforeach
            </select>
            
            <label>Fecha *</label>
            <input type="date" name="fecha" id="fecha" required>
            
            <div id="horariosContainer" style="display: none;">
                <h3>📅 Horarios disponibles</h3>
                <div id="horariosLista" class="horarios"></div>
            </div>
            
            <input type="hidden" name="hora_inicio" id="hora_inicio">
            <input type="hidden" name="hora_fin" id="hora_fin">
            
            <button type="submit" id="submitBtn" style="display: none;">📨 Solicitar Cita</button>
        </form>
        
        <br>
        <a href="/cliente/dashboard?token={{ $token }}">← Volver al Dashboard</a>
    </div>

    <script>
        $('#id_mascota, #id_servicio, #id_empleado, #fecha').change(function() {
            cargarHorarios();
        });
        
        function cargarHorarios() {
            const mascotaId = $('#id_mascota').val();
            const servicioId = $('#id_servicio').val();
            const empleadoId = $('#id_empleado').val();
            const fecha = $('#fecha').val();
            
            if (!mascotaId || !servicioId || !empleadoId || !fecha) {
                $('#horariosContainer').hide();
                $('#submitBtn').hide();
                return;
            }
            
            $('#horariosContainer').show();
            $('#horariosLista').html('<div>Cargando horarios...</div>');
            
            $.ajax({
                url: '/admin/citas/horarios',
                method: 'GET',
                data: {
                    mascota_id: mascotaId,
                    servicio_id: servicioId,
                    empleado_id: empleadoId,
                    fecha: fecha,
                    token: '{{ $token }}'
                },
                success: function(data) {
                    if (data.length === 0) {
                        $('#horariosLista').html('<div style="color: red;">No hay horarios disponibles para este día</div>');
                        $('#submitBtn').hide();
                    } else {
                        let html = '';
                        data.forEach(slot => {
                            html += `<div class="slot" onclick="seleccionarHorario('${slot.hora_inicio}', '${slot.hora_fin}')">
                                        🕐 ${slot.hora_inicio} - ${slot.hora_fin} (${slot.duracion} min)
                                    </div>`;
                        });
                        $('#horariosLista').html(html);
                        $('#submitBtn').show();
                    }
                },
                error: function() {
                    $('#horariosLista').html('<div style="color: red;">Error al cargar horarios</div>');
                }
            });
        }
        
        function seleccionarHorario(inicio, fin) {
            $('#hora_inicio').val(inicio);
            $('#hora_fin').val(fin);
            $('.slot').css('background', '#e3f2fd');
            $(event.target).css('background', '#4CAF50').css('color', 'white');
        }
    </script>
</body>
</html>