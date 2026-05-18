<!DOCTYPE html>
<html>
<head>
    <title>Agenda Maestra - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1400px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; border: none; cursor: pointer; font-size: 14px; }
        .btn-primary { background: white; color: #4CAF50; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }
        .btn-danger { background: #f44336; color: white; }
        .btn-danger:hover { background: #d32f2f; }
        .btn-success { background: #4CAF50; color: white; }
        .btn-success:hover { background: #45a049; transform: translateY(-2px); }
        .content { padding: 30px; }
        
        /* Navegación de fechas */
        .fecha-nav { background: #f8f9fa; padding: 15px 20px; border-radius: 16px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .fecha-controls { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .fecha-actual { font-size: 18px; font-weight: 600; color: #333; background: white; padding: 8px 20px; border-radius: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .fecha-actual i { margin-right: 8px; color: #4CAF50; }
        
        /* Grid de agenda */
        .agenda-grid { display: flex; overflow-x: auto; gap: 15px; padding-bottom: 10px; }
        .columna-groomer { min-width: 340px; background: #f8f9fa; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .groomer-header { background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); color: white; padding: 15px; text-align: center; }
        .groomer-header h3 { font-size: 16px; margin-bottom: 5px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .groomer-header p { font-size: 12px; opacity: 0.9; }
        .citas-container { min-height: 500px; padding: 12px; max-height: 600px; overflow-y: auto; }
        
        /* Tarjetas de cita */
        .cita-card { background: white; border-radius: 12px; padding: 12px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.2s; border-left: 4px solid; }
        .cita-card:hover { transform: translateX(4px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .cita-hora { font-weight: 700; font-size: 14px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .cita-mascota { font-weight: 600; font-size: 14px; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
        .cita-servicio { font-size: 12px; color: #666; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .cita-actions { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
        .btn-icon { padding: 5px 12px; font-size: 11px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; border: none; font-weight: 500; }
        .btn-edit { background: #2196F3; color: white; }
        .btn-edit:hover { background: #1976D2; }
        .btn-cancel { background: #f44336; color: white; }
        .btn-cancel:hover { background: #d32f2f; }
        .btn-accept { background: #4CAF50; color: white; }
        .btn-accept:hover { background: #45a049; }
        
        /* Estados de cita */
        .cita-programado { border-left-color: #4CAF50; }
        .cita-reservado { border-left-color: #ff9800; background: #fff8f0; }
        .cita-concluido { border-left-color: #2196F3; background: #f0f7ff; }
        .estado-badge { font-size: 10px; padding: 2px 8px; border-radius: 20px; display: inline-block; }
        .estado-programado { background: #e8f5e9; color: #4CAF50; }
        .estado-reservado { background: #fff3e0; color: #ff9800; }
        .estado-concluido { background: #e3f2fd; color: #2196F3; }
        
        .empty-message { text-align: center; padding: 40px; color: #999; }
        .empty-message i { font-size: 48px; margin-bottom: 10px; display: block; }
        
        /* Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 20px; width: 450px; max-width: 90%; padding: 25px; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .modal-content h3 { margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .modal-content select, .modal-content textarea { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 12px; margin: 10px 0; font-family: 'Inter', sans-serif; }
        .modal-content select:focus, .modal-content textarea:focus { outline: none; border-color: #4CAF50; }
        .modal-buttons { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        
        @media (max-width: 768px) { .fecha-nav { flex-direction: column; } .columna-groomer { min-width: 280px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calendar-alt"></i> Agenda Maestra</h1>
            <div style="display: flex; gap: 10px;">
                <a href="/admin/citas/create?token={{ $token }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Nueva Cita
                </a>
                @if($rol == 'admin')
                    <a href="/admin/dashboard?token={{ $token }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                @else
                    <a href="/recepcion/dashboard?token={{ $token }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                @endif
            </div>
        </div>
        
        <div class="content">
            <!-- Navegación de fechas -->
            <div class="fecha-nav">
                <div class="fecha-controls">
                    <a href="?fecha={{ Carbon\Carbon::parse($fecha)->subDay()->format('Y-m-d') }}&token={{ $token }}" class="btn" style="background: #2196F3; color: white; padding: 10px 18px; border-radius: 10px;">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                    <a href="?fecha={{ Carbon\Carbon::now()->format('Y-m-d') }}&token={{ $token }}" class="btn" style="background: #4CAF50; color: white; padding: 10px 18px; border-radius: 10px;">
                        <i class="fas fa-calendar-day"></i> Hoy
                    </a>
                    <a href="?fecha={{ Carbon\Carbon::parse($fecha)->addDay()->format('Y-m-d') }}&token={{ $token }}" class="btn" style="background: #2196F3; color: white; padding: 10px 18px; border-radius: 10px;">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <div class="fecha-actual" style="background: #e8f5e9; padding: 10px 20px; border-radius: 10px; font-weight: 600;">
                    <i class="fas fa-calendar-alt" style="color: #4CAF50;"></i> {{ $fechaObj->format('d/m/Y') }} | <strong>{{ $fechaObj->translatedFormat('l') }}</strong>
                </div>
            </div>
            
            <!-- Grid de agenda -->
            <div class="agenda-grid">
                @foreach($groomers as $groomer)
                <div class="columna-groomer">
                    <div class="groomer-header">
                        <h3><i class="fas fa-cut"></i> {{ $groomer->usuario->nombres }} {{ $groomer->usuario->apellidos }}</h3>
                        <p><i class="fas fa-briefcase"></i> {{ $groomer->cargo }}</p>
                    </div>
                    <div class="citas-container">
                        @php
                            $citasGroomer = $citas->where('id_empleado', $groomer->id_empleado);
                        @endphp
                        @if($citasGroomer->count() > 0)
                            @foreach($citasGroomer as $cita)
                                @if(in_array($cita->estado, ['programado', 'reservado', 'concluido']))
                               @php
    $borderColor = '#2196F3';
    if($cita->estado == 'programado') $borderColor = '#4CAF50';
    if($cita->estado == 'reservado') $borderColor = '#ff9800';
@endphp
<div class="cita-card cita-{{ $cita->estado }}" style="border-left-color: '{{ $borderColor }}';">
                                    <div class="cita-hora">
                                        <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($cita->hora_fin)->format('H:i') }}
                                        <span class="estado-badge 
                                            @if($cita->estado == 'programado') estado-programado
                                            @elseif($cita->estado == 'reservado') estado-reservado
                                            @else estado-concluido @endif">
                                            @if($cita->estado == 'programado') ✅ Confirmada
                                            @elseif($cita->estado == 'reservado') ⏳ Pendiente
                                            @else 🏁 Concluida
                                            @endif
                                        </span>
                                    </div>
                                    <div class="cita-mascota">
                                        <i class="fas fa-dog"></i> {{ $cita->mascota->nombre }}
                                        <small>({{ $cita->mascota->especie }})</small>
                                    </div>
                                    <div class="cita-servicio">
                                        <i class="fas fa-cut"></i> {{ $cita->servicio->nombre }}
                                    </div>
                                    <div class="cita-actions">
                                        <!-- Botón Editar - Solo si NO está concluida -->
                                        @if($cita->estado != 'concluido')
                                            <a href="/admin/citas/{{ $cita->id_cita }}/edit?token={{ $token }}" class="btn-icon btn-edit">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        @endif
                                        
                                        <!-- Botón Aceptar - Solo si está pendiente (reservado) -->
                                        @if($cita->estado == 'reservado')
                                            <a href="#" onclick="confirmarCita('{{ $cita->id_cita }}', '{{ $token }}')" class="btn-icon btn-accept">
                                                <i class="fas fa-check-circle"></i> Aceptar Cita
                                            </a>
                                        @endif
                                        
                                        <!-- Botón Cancelar - Solo si no está cancelada ni concluida -->
                                        @if($cita->estado != 'cancelado' && $cita->estado != 'concluido')
                                            <a href="#" onclick="mostrarModalCancelar('{{ $cita->id_cita }}', '{{ $token }}')" class="btn-icon btn-cancel">
                                                <i class="fas fa-times-circle"></i> Cancelar Cita
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @else
                            <div class="empty-message">
                                <i class="fas fa-calendar-day"></i>
                                <p>Sin citas programadas</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal para cancelar cita -->
    <div id="modalCancelar" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-exclamation-triangle" style="color: #f44336;"></i> Cancelar Cita</h3>
            <label>Motivo de cancelación:</label>
            <select id="motivo_cancelacion">
                <option value="Salud">Salud (mascota o dueño)</option>
                <option value="Emergencia">Emergencia</option>
                <option value="Cambio de horario">Cambio de horario</option>
                <option value="Cliente no asistió">Cliente no asistió</option>
                <option value="Otro">Otro</option>
            </select>
            <label>Observaciones (opcional):</label>
            <textarea id="observaciones_cancelacion" rows="3" placeholder="Escribe aquí..."></textarea>
            <div class="modal-buttons">
                <button onclick="confirmarCancelacion()" class="btn btn-danger">✅ Confirmar</button>
                <button onclick="cerrarModalCancelar()" class="btn btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        let citaIdCancelar = null;
        let tokenCancelar = null;
        
        // =========================================================
        // MOSTRAR MODAL DE CANCELACIÓN
        // =========================================================
        function mostrarModalCancelar(citaId, token) {
            citaIdCancelar = citaId;
            tokenCancelar = token;
            document.getElementById('modalCancelar').style.display = 'flex';
        }
        
        // =========================================================
        // CERRAR MODAL DE CANCELACIÓN
        // =========================================================
        function cerrarModalCancelar() {
            document.getElementById('modalCancelar').style.display = 'none';
            citaIdCancelar = null;
            document.getElementById('motivo_cancelacion').value = 'Salud';
            document.getElementById('observaciones_cancelacion').value = '';
        }
        
        // =========================================================
        // CONFIRMAR CANCELACIÓN
        // =========================================================
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
                cerrarModalCancelar();
            });
        }
        
        // =========================================================
        // CONFIRMAR/ACEPTAR CITA (NUEVA FUNCIÓN)
        // =========================================================
        function confirmarCita(citaId, token) {
            if (confirm('¿Estás seguro de aceptar esta cita? Se programará automáticamente.')) {
                fetch('/admin/citas/' + citaId + '/confirmar?token=' + token, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Cita confirmada correctamente');
                        location.reload();
                    } else {
                        alert('❌ Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ Error al confirmar la cita');
                });
            }
        }
        
        // =========================================================
        // CERRAR MODAL AL HACER CLIC FUERA
        // =========================================================
        window.onclick = function(event) {
            const modal = document.getElementById('modalCancelar');
            if (event.target == modal) {
                cerrarModalCancelar();
            }
        }
    </script>
</body>
</html>