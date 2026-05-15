<!DOCTYPE html>
<html>
<head>
    <title>Mis Citas - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #fff3e0; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #FF9800; color: white; }
        .pendiente { color: orange; font-weight: bold; }
        .programado { color: green; font-weight: bold; }
        .cancelado { color: red; font-weight: bold; }
        .btn { background: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 15px; }
        .btn-cancelar { background: #f44336; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; border: none; cursor: pointer; }
        .btn-cancelar:hover { background: #d32f2f; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
        }
        .modal-content select, .modal-content textarea {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .modal-content button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-confirm { background: #f44336; color: white; }
        .btn-cancel { background: #607d8b; color: white; margin-left: 10px; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>🐾 Mis Citas</h1>
        
        @php $token = request()->query('token'); @endphp
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        
        <a href="/cliente/solicitar-cita?token={{ $token }}" class="btn">+ Solicitar Nueva Cita</a>
        <a href="/cliente/dashboard?token={{ $token }}" style="margin-left: 10px;">← Volver</a>
        
        <table style="margin-top: 20px;">
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
                    <td>{{ $cita->mascota->nombre }} ({{ $cita->mascota->especie }})</td>
                    <td>{{ $cita->servicio->nombre }}</td>
                    <td>{{ $cita->empleado->usuario->nombres }} {{ $cita->empleado->usuario->apellidos }}</td>
                    <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</td>
                    <td class="{{ $cita->estado }}">
                        @if($cita->estado == 'reservado')
                            ⏳ Pendiente
                        @elseif($cita->estado == 'programado')
                            ✅ Confirmada
                        @else
                            ❌ Cancelada
                        @endif
                    </td>
                    <td>
                        @if($cita->estado == 'reservado' || $cita->estado == 'programado')
                            <button class="btn-cancelar" onclick="mostrarModalCancelar('{{ $cita->id_cita }}')">❌ Cancelar</button>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
                <option value="Otro">Otro</option>
            </select>
            
            <label>Observaciones (opcional):</label>
            <textarea id="observaciones_cancelacion" placeholder="Escribe aquí..."></textarea>
            
            <button class="btn-confirm" onclick="confirmarCancelacion()">✅ Confirmar Cancelación</button>
            <button class="btn-cancel" onclick="cerrarModalCancelar()">Cancelar</button>
        </div>
    </div>

    <script>
        let citaIdCancelar = null;

        function mostrarModalCancelar(citaId) {
            citaIdCancelar = citaId;
            document.getElementById('modalCancelar').style.display = 'block';
        }

        function cerrarModalCancelar() {
            document.getElementById('modalCancelar').style.display = 'none';
            citaIdCancelar = null;
            document.getElementById('motivo_cancelacion').value = 'Salud';
            document.getElementById('observaciones_cancelacion').value = '';
        }

        function confirmarCancelacion() {
            const token = '{{ $token }}';
            const motivo = document.getElementById('motivo_cancelacion').value;
            const observaciones = document.getElementById('observaciones_cancelacion').value;
            
            fetch('/cliente/citas/' + citaIdCancelar + '/cancelar?token=' + token, {
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