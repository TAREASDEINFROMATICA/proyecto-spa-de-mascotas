<!DOCTYPE html>
<html>
<head>
    <title>Editar Cita - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 50px 20px; 
        }
        .container { 
            max-width: 700px; 
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
        }
        .page-header h2 {
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }
        .page-header h2 i { font-size: 28px; color: #ffd700; }
        
        /* Contenido */
        .content { padding: 30px; }
        
        /* Info actual */
        .info-actual {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            padding: 18px 20px;
            border-radius: 16px;
            margin-bottom: 25px;
            border-left: 4px solid #4CAF50;
        }
        .info-actual strong {
            color: #2e7d32;
            font-size: 14px;
            display: block;
            margin-bottom: 10px;
        }
        .info-actual .info-row {
            margin: 8px 0;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .info-actual .info-row i {
            width: 24px;
            color: #4CAF50;
        }
        
        /* Campos */
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: #1e293b;
            margin-bottom: 6px;
        }
        label i {
            margin-right: 6px;
            color: #4CAF50;
            width: 18px;
        }
        input, select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            background: white;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76,175,80,0.1);
        }
        
        /* Hora seleccionada */
        .hora-seleccionada {
            background: #e3f2fd;
            padding: 12px 15px;
            border-radius: 12px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #2196F3;
        }
        .hora-seleccionada i {
            font-size: 18px;
            color: #2196F3;
        }
        .hora-seleccionada strong {
            color: #1565c0;
        }
        
        /* Horarios */
        .horarios-section {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }
        .horarios-section h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .horarios-section h3 i { color: #4CAF50; }
        
        .horarios-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
        }
        .slot {
            background: #f1f5f9;
            padding: 10px 18px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            border: 1px solid #e2e8f0;
        }
        .slot:hover {
            background: #e2e8f0;
            transform: scale(1.02);
        }
        .slot-seleccionado {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            border-color: #4CAF50;
        }
        
        .loading-message {
            text-align: center;
            padding: 20px;
            color: #64748b;
        }
        .no-horarios {
            text-align: center;
            padding: 20px;
            color: #f44336;
            background: #ffebee;
            border-radius: 12px;
        }
        .mensaje-info {
            text-align: center;
            padding: 20px;
            color: #ff9800;
            background: #fff3e0;
            border-radius: 12px;
        }
        
        /* Errores */
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
            font-size: 14px;
        }
        
        /* Botón enviar */
        .btn-submit {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 700;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76,175,80,0.3);
        }
        
        /* Botón volver */
        .back-link {
            margin-top: 20px;
            text-align: center;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
        
        @media (max-width: 600px) {
            .container { margin: 0 10px; border-radius: 20px; }
            .content { padding: 20px; }
            .slot { padding: 8px 14px; font-size: 12px; }
            .info-row { flex-direction: column; align-items: flex-start !important; gap: 5px !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2>
                <i class="fas fa-edit"></i>
                Editar Cita
            </h2>
        </div>
        
        <div class="content">
            @php $token = request()->query('token'); @endphp
            
            <!-- Mostrar información actual de la cita -->
            <div class="info-actual">
                <strong><i class="fas fa-info-circle"></i> Información actual de la cita:</strong>
                <div class="info-row">
                    <i class="fas fa-dog"></i>
                    <span><strong>Mascota:</strong> {{ $cita->mascota->nombre }}</span>
                </div>
                <div class="info-row">
                    <i class="fas fa-cut"></i>
                    <span><strong>Servicio:</strong> {{ $cita->servicio->nombre }}</span>
                </div>
                <div class="info-row">
                    <i class="fas fa-user"></i>
                    <span><strong>Groomer:</strong> {{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}</span>
                </div>
                <div class="info-row">
                    <i class="fas fa-calendar"></i>
                    <span><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <i class="fas fa-clock"></i>
                    <span><strong>Hora:</strong> {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</span>
                </div>
            </div>
            
            @if($errors->any())
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
            
            <form id="citaForm" method="POST" action="/admin/citas/{{ $cita->id_cita }}?token={{ $token }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="token" value="{{ $token }}">
                <div id="citaData" data-cita-id="{{ $cita->id_cita }}" style="display: none;"></div>
                
                <div class="form-group">
                    <label><i class="fas fa-dog"></i> Mascota *</label>
                    <select name="id_mascota" id="id_mascota" required>
                        <option value="">Seleccionar mascota</option>
                        @foreach($mascotas as $m)
                            <option value="{{ $m->id_mascota }}" {{ $cita->id_mascota == $m->id_mascota ? 'selected' : '' }}>
                                {{ $m->nombre }} ({{ $m->cliente->usuario->nombres }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-cut"></i> Servicio *</label>
                    <select name="id_servicio" id="id_servicio" required>
                        <option value="">Seleccionar servicio</option>
                        @foreach($servicios as $s)
                            <option value="{{ $s->id_servicio }}" {{ $cita->id_servicio == $s->id_servicio ? 'selected' : '' }}>
                                {{ $s->nombre }} ({{ $s->duracion_minutos }} min - Bs {{ number_format($s->precio, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Groomer *</label>
                    <select name="id_empleado" id="id_empleado" required>
                        <option value="">Seleccionar groomer</option>
                        @foreach($groomers as $g)
                            <option value="{{ $g->id_empleado }}" {{ $cita->id_empleado == $g->id_empleado ? 'selected' : '' }}>
                                ✂️ {{ $g->usuario->nombres }} {{ $g->usuario->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Fecha *</label>
                    <input type="date" name="fecha" id="fecha" value="{{ $cita->fecha }}" required>
                </div>
                
                <!-- Hora seleccionada actualmente -->
                <div class="hora-seleccionada">
                    <i class="fas fa-clock"></i>
                    <span><strong>Hora seleccionada actualmente:</strong> <span id="horaSeleccionadaDisplay">{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</span></span>
                </div>
                
                <div id="horariosContainer">
                    <div class="horarios-section">
                        <h3><i class="fas fa-clock"></i> Horarios disponibles</h3>
                        <div id="horariosLista" class="horarios-grid">
                            <div class="mensaje-info">
                                <i class="fas fa-info-circle"></i> Complete todos los campos para ver horarios disponibles
                            </div>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ $cita->hora_inicio }}">
                <input type="hidden" name="hora_fin" id="hora_fin" value="{{ $cita->hora_fin }}">
                
                <button type="submit" id="submitBtn" class="btn-submit">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>
            
            <div class="back-link">
                <a href="/admin/agenda?fecha={{ $cita->fecha }}&token={{ $token }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Volver a la Agenda
                </a>
            </div>
        </div>
    </div>

    <script>
        // Obtener citaId desde el atributo data
        const citaId = document.getElementById('citaData')?.getAttribute('data-cita-id') || '{{ $cita->id_cita }}';
        const token = '{{ $token }}';
        
        function actualizarHoraDisplay(inicio, fin) {
            $('#horaSeleccionadaDisplay').text(inicio + ' - ' + fin);
        }
        
        function cargarHorarios() {
            const mascotaId = $('#id_mascota').val();
            const servicioId = $('#id_servicio').val();
            const empleadoId = $('#id_empleado').val();
            const fecha = $('#fecha').val();
            
            if (!mascotaId || !servicioId || !empleadoId || !fecha) {
                $('#horariosLista').html('<div class="mensaje-info"><i class="fas fa-info-circle"></i> Complete todos los campos para ver horarios disponibles</div>');
                return;
            }
            
            $('#horariosLista').html('<div class="loading-message"><i class="fas fa-spinner fa-spin"></i> Cargando horarios...</div>');
            
            $.ajax({
                url: '/admin/citas/horarios',
                method: 'GET',
                data: {
                    mascota_id: mascotaId,
                    servicio_id: servicioId,
                    empleado_id: empleadoId,
                    fecha: fecha,
                    cita_id: citaId,
                    token: token
                },
                success: function(data) {
                    console.log('Horarios recibidos:', data);
                    if (data.length === 0) {
                        $('#horariosLista').html('<div class="no-horarios"><i class="fas fa-calendar-times"></i> No hay horarios disponibles para este día</div>');
                    } else {
                        let html = '';
                        const horaActualInicio = $('#hora_inicio').val();
                        data.forEach(slot => {
                            const seleccionado = (slot.hora_inicio === horaActualInicio);
                            html += `<div class="slot ${seleccionado ? 'slot-seleccionado' : ''}" 
                                        onclick="seleccionarHorario('${slot.hora_inicio}', '${slot.hora_fin}')">
                                        <i class="fas fa-clock"></i> ${slot.hora_inicio} - ${slot.hora_fin} (${slot.duracion} min)
                                    </div>`;
                        });
                        $('#horariosLista').html(html);
                    }
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                    $('#horariosLista').html('<div class="no-horarios"><i class="fas fa-exclamation-triangle"></i> Error al cargar horarios</div>');
                }
            });
        }
        
        function seleccionarHorario(inicio, fin) {
            $('#hora_inicio').val(inicio);
            $('#hora_fin').val(fin);
            actualizarHoraDisplay(inicio, fin);
            $('.slot').removeClass('slot-seleccionado');
            $('.slot').css('background', '#f1f5f9');
            $('.slot').css('color', '#334155');
            $(event.target).closest('.slot').addClass('slot-seleccionado');
            $(event.target).closest('.slot').css('background', '');
            $(event.target).closest('.slot').css('color', '');
        }
        
        $(document).ready(function() {
            $('#id_mascota, #id_servicio, #id_empleado, #fecha').on('change', cargarHorarios);
            cargarHorarios();
        });
    </script>
</body>
</html>