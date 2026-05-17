<!DOCTYPE html>
<html>
<head>
    <title>Mis Citas - Pet Spa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; display: flex; align-items: center; justify-content: center; gap: 12px; }
        .header p { opacity: 0.9; margin-top: 8px; }
        .content { padding: 30px; }
        .btn { padding: 10px 20px; border: none; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; }
        .btn-primary { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(76,175,80,0.3); }
        .btn-secondary { background: #607d8b; color: white; }
        .btn-secondary:hover { background: #546e7a; }
        .btn-cancelar { background: #f44336; color: white; padding: 6px 12px; font-size: 12px; border-radius: 8px; }
        .btn-cancelar:hover { background: #d32f2f; }
        .btn-calificar { background: #ff9800; color: white; padding: 6px 12px; font-size: 12px; border-radius: 8px; border: none; cursor: pointer; }
        .btn-calificar:hover { background: #f57c00; }
        .btn-ver { background: #2196F3; color: white; padding: 6px 12px; font-size: 12px; border-radius: 8px; text-decoration: none; }
        .btn-ver:hover { background: #1976D2; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #e0e0e0; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        tr:hover { background: #f9f9f9; }
        .estado-reservado { background: #fff3e0; color: #ff9800; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .estado-programado { background: #e8f5e9; color: #4CAF50; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .estado-concluido { background: #e3f2fd; color: #2196F3; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .estado-cancelado { background: #ffebee; color: #f44336; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .section-title { font-size: 20px; font-weight: 600; color: #333; margin: 30px 0 20px 0; display: flex; align-items: center; gap: 10px; }
        .section-title i { color: #FF9800; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .empty-message { text-align: center; padding: 40px; color: #999; }
        .empty-message i { font-size: 48px; margin-bottom: 15px; display: block; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 20px; width: 450px; max-width: 90%; padding: 25px; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .modal-content h3 { margin-bottom: 20px; color: #333; display: flex; align-items: center; gap: 10px; }
        .modal-content select, .modal-content textarea { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 12px; margin: 10px 0; font-family: 'Inter', sans-serif; transition: all 0.3s; }
        .modal-content select:focus, .modal-content textarea:focus { outline: none; border-color: #FF9800; }
        .modal-content button { padding: 10px 20px; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; margin-top: 10px; }
        .btn-confirm { background: #f44336; color: white; }
        .btn-cancel { background: #607d8b; color: white; margin-left: 10px; }
        .estrellas { display: flex; justify-content: center; gap: 15px; margin: 20px 0; cursor: pointer; }
        .estrella { font-size: 45px; color: #ddd; transition: color 0.2s; }
        .estrella.seleccionada, .estrella:hover { color: #ffc107; }
        .calificacion-info { display: flex; align-items: center; gap: 10px; }
        .calificacion-estrellas { color: #ffc107; font-size: 14px; }
        .header-buttons { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
        @media (max-width: 768px) { table { font-size: 12px; } th, td { padding: 8px; } .btn-cancelar, .btn-calificar, .btn-ver { padding: 4px 8px; font-size: 10px; } }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calendar-alt"></i> Mis Citas</h1>
            <p>Gestiona tus citas y califica los servicios recibidos</p>
        </div>
        
        <div class="content">
            @php $token = request()->query('token'); @endphp
            
            @if(session('success'))
                <div class="success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            <div class="header-buttons">
                <a href="/cliente/solicitar-cita?token={{ $token }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Solicitar Nueva Cita
                </a>
                <a href="/cliente/dashboard?token={{ $token }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
            
            <!-- ========================================================= -->
            <!-- CITAS ACTIVAS -->
            <!-- ========================================================= -->
            <div class="section-title">
                <i class="fas fa-clock"></i>
                <span>Citas Activas</span>
            </div>
            
            @if($citas->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mascota</th>
                        <th>Servicio</th>
                        <th>Groomer</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citas as $cita)
                    <tr>
                        <td>{{ $cita->id_cita }}</td>
                        <td>{{ $cita->mascota->nombre }} <small>({{ $cita->mascota->especie }})</small></td>
                        <td>{{ $cita->servicio->nombre }}</td>
                        <td>{{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($cita->hora_fin)->format('H:i') }}</td>
                        <td>
                            <span class="estado-{{ $cita->estado }}">
                                @if($cita->estado == 'reservado')
                                    <i class="fas fa-hourglass-half"></i> Pendiente
                                @elseif($cita->estado == 'programado')
                                    <i class="fas fa-check-circle"></i> Confirmada
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($cita->estado == 'reservado' || $cita->estado == 'programado')
                                <button class="btn-cancelar" onclick="mostrarModalCancelar('{{ $cita->id_cita }}')">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="empty-message">
                    <i class="fas fa-calendar-check"></i>
                    <p>No tienes citas activas</p>
                    <a href="/cliente/solicitar-cita?token={{ $token }}" class="btn btn-primary" style="margin-top: 15px;">Solicitar una cita</a>
                </div>
            @endif
            
            <!-- ========================================================= -->
            <!-- SERVICIOS POR CALIFICAR -->
            <!-- ========================================================= -->
            <div class="section-title">
                <i class="fas fa-star"></i>
                <span>Servicios por Calificar</span>
            </div>
            
            @if(isset($citasPorCalificar) && $citasPorCalificar->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mascota</th>
                        <th>Servicio</th>
                        <th>Groomer</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citasPorCalificar as $cita)
                    <tr>
                        <td>{{ $cita->id_cita }}</td>
                        <td>{{ $cita->mascota->nombre }} <small>({{ $cita->mascota->especie }})</small></td>
                        <td>{{ $cita->servicio->nombre }}</td>
                        <td>{{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($cita->hora_fin)->format('H:i') }}</td>
                        <td>
                            <button class="btn-calificar" onclick="mostrarModalCalificar('{{ $cita->id_cita }}')">
                                <i class="fas fa-star"></i> Calificar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="empty-message">
                    <i class="fas fa-smile-wink"></i>
                    <p>No hay servicios pendientes de calificar</p>
                </div>
            @endif
            
            <!-- ========================================================= -->
            <!-- HISTORIAL DE SERVICIOS CALIFICADOS -->
            <!-- ========================================================= -->
            <div class="section-title">
                <i class="fas fa-history"></i>
                <span>Historial de Servicios</span>
            </div>
            
            @if(isset($citasCalificadas) && $citasCalificadas->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Mascota</th>
                        <th>Servicio</th>
                        <th>Groomer</th>
                        <th>Fecha</th>
                        <th>Tu Calificación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citasCalificadas as $cita)
                    <tr>
                        <td>{{ $cita->mascota->nombre }}</td>
                        <td>{{ $cita->servicio->nombre }}</td>
                        <td>{{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                        <td>
                            <div class="calificacion-info">
                                <div class="calificacion-estrellas">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $cita->calificacion->puntuacion ? '' : 'far' }}"></i>
                                    @endfor
                                </div>
                                <small style="color: #666;">"{{ $cita->calificacion->comentario ?? 'Sin comentario' }}"</small>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="empty-message">
                    <i class="fas fa-history"></i>
                    <p>No hay servicios en el historial</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para cancelar cita -->
    <div id="modalCancelar" class="modal" style="display: none;">
        <div class="modal-content">
            <h3><i class="fas fa-times-circle" style="color: #f44336;"></i> Cancelar Cita</h3>
            <label>Motivo de cancelación:</label>
            <select id="motivo_cancelacion">
                <option value="Salud">Salud (mascota o dueño)</option>
                <option value="Emergencia">Emergencia</option>
                <option value="Cambio de horario">Cambio de horario</option>
                <option value="Otro">Otro</option>
            </select>
            <label>Observaciones (opcional):</label>
            <textarea id="observaciones_cancelacion" rows="3" placeholder="Escribe aquí..."></textarea>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button class="btn-confirm" onclick="confirmarCancelacion()">✅ Confirmar</button>
                <button class="btn-cancel" onclick="cerrarModalCancelar()">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Modal para calificar servicio -->
    <div id="modalCalificar" class="modal" style="display: none;">
        <div class="modal-content" style="text-align: center;">
            <h3><i class="fas fa-star" style="color: #ffc107;"></i> Calificar Servicio</h3>
            <div class="estrellas">
                <span class="estrella" data-puntuacion="1" onclick="seleccionarPuntuacion(1)">☆</span>
                <span class="estrella" data-puntuacion="2" onclick="seleccionarPuntuacion(2)">☆</span>
                <span class="estrella" data-puntuacion="3" onclick="seleccionarPuntuacion(3)">☆</span>
                <span class="estrella" data-puntuacion="4" onclick="seleccionarPuntuacion(4)">☆</span>
                <span class="estrella" data-puntuacion="5" onclick="seleccionarPuntuacion(5)">☆</span>
            </div>
            <label>Tu comentario (opcional):</label>
            <textarea id="comentario_calificacion" rows="3" placeholder="¿Cómo fue tu experiencia?"></textarea>
            <input type="hidden" id="puntuacion_seleccionada" value="0">
            <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px;">
                <button class="btn-confirm" style="background: #4CAF50;" onclick="enviarCalificacion()">✅ Enviar</button>
                <button class="btn-cancel" onclick="cerrarModalCalificar()">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        let citaIdCancelar = null;
        let citaIdCalificar = null;
        const token = '{{ $token }}';

        // =========================================================
        // CANCELAR CITA
        // =========================================================
        function mostrarModalCancelar(citaId) {
            citaIdCancelar = citaId;
            document.getElementById('modalCancelar').style.display = 'flex';
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
            
            fetch('/cliente/citas/' + citaIdCancelar + '/cancelar?token=' + token, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ motivo: motivo, observaciones: observaciones })
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

        // =========================================================
        // CALIFICAR SERVICIO
        // =========================================================
        function mostrarModalCalificar(citaId) {
            citaIdCalificar = citaId;
            document.getElementById('modalCalificar').style.display = 'flex';
            document.querySelectorAll('.estrella').forEach(star => {
                star.classList.remove('seleccionada');
                star.innerHTML = '☆';
            });
            document.getElementById('puntuacion_seleccionada').value = 0;
            document.getElementById('comentario_calificacion').value = '';
        }

        function cerrarModalCalificar() {
            document.getElementById('modalCalificar').style.display = 'none';
            citaIdCalificar = null;
        }

        function seleccionarPuntuacion(puntuacion) {
            document.getElementById('puntuacion_seleccionada').value = puntuacion;
            document.querySelectorAll('.estrella').forEach((star, index) => {
                if (index < puntuacion) {
                    star.classList.add('seleccionada');
                    star.innerHTML = '★';
                } else {
                    star.classList.remove('seleccionada');
                    star.innerHTML = '☆';
                }
            });
        }

        function enviarCalificacion() {
            const puntuacion = document.getElementById('puntuacion_seleccionada').value;
            const comentario = document.getElementById('comentario_calificacion').value;
            
            if (puntuacion == 0) {
                alert('❌ Selecciona una puntuación de 1 a 5 estrellas');
                return;
            }
            
            fetch('/cliente/calificar/' + citaIdCalificar + '?token=' + token, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ puntuacion: puntuacion, comentario: comentario })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ¡Gracias por tu calificación!');
                    location.reload();
                } else {
                    alert('❌ Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('❌ Error al enviar la calificación');
            });
        }
    </script>
</body>
</html>