<!DOCTYPE html>
<html>
<head>
    <title>Ficha Técnica - {{ $cita->mascota->nombre }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .seccion { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 8px; }
        h2 { color: #4CAF50; }
        h3 { color: #2196F3; }
        input, select, textarea { width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 5px; }
        .btn { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn-guardar { background: #ff9800; color: white; }
        .btn-cerrar { background: #2196F3; }
        .btn-volver { background: #607d8b; }
        .checklist-item { margin: 5px 0; }
        .checklist-item input { width: auto; margin-right: 10px; }
        .fotos { display: flex; gap: 20px; flex-wrap: wrap; }
        .foto-card { text-align: center; }
        .foto-card img { width: 200px; height: 150px; object-fit: cover; border-radius: 8px; margin: 5px; }
        .disabled-field { background: #e9ecef; color: #6c757d; cursor: not-allowed; }
        .alerta-cerrado { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f44336; }
        .alerta-exito { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Ficha Técnica</h1>
        
        @php 
            $token = request()->query('token');
            $estaCerrado = ($cita->estado === 'concluido');
        @endphp
        
        @if($estaCerrado)
        <div class="alerta-cerrado">
            ⚠️ <strong>Este servicio ya está cerrado</strong> - No se pueden realizar más cambios.
        </div>
        @endif
        
        <!-- Datos de la mascota -->
        <div class="seccion">
            <h2>🐕 Datos de la Mascota</h2>
            <p><strong>Nombre:</strong> {{ $cita->mascota->nombre }}</p>
            <p><strong>Especie:</strong> {{ $cita->mascota->especie }}</p>
            <p><strong>Raza:</strong> {{ $cita->mascota->raza ?? '-' }}</p>
            <p><strong>Edad:</strong> {{ $cita->mascota->fecha_nacimiento ? \Carbon\Carbon::parse($cita->mascota->fecha_nacimiento)->age . ' años' : '-' }}</p>
            <p><strong>Temperamento:</strong> {{ ucfirst($cita->mascota->temperamento_general ?? '-') }}</p>
            <p><strong>Alergias:</strong> {{ $cita->mascota->alergias ?? '-' }}</p>
            <p><strong>Cuidados especiales:</strong> {{ $cita->mascota->cuidados_especiales ?? '-' }}</p>
        </div>
        
        <!-- Datos del servicio -->
        <div class="seccion">
            <h2>✂️ Datos del Servicio</h2>
            <p><strong>Servicio:</strong> {{ $cita->servicio->nombre }}</p>
            <p><strong>Duración:</strong> {{ $cita->servicio->duracion_minutos }} minutos</p>
            <p><strong>Hora:</strong> {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</p>
        </div>
        
        <!-- Estado de ingreso -->
        <div class="seccion">
            <h2>📝 Estado de Ingreso</h2>
            <textarea id="estado_ingreso" rows="3" placeholder="Describa el estado inicial de la mascota (nudos, heridas, pulgas, comportamiento, etc.)" {{ $estaCerrado ? 'disabled' : '' }}>{{ $datosGuardados['estado_ingreso'] ?? '' }}</textarea>
        </div>
        
        <!-- Checklist -->
        <div class="seccion">
            <h2>✅ Checklist de Tareas</h2>
            @php
                $checklistGuardado = $datosGuardados['checklist'] ?? [];
            @endphp
            <div class="checklist-item">
                <input type="checkbox" id="check_baño" value="Baño" {{ in_array('Baño', $checklistGuardado) ? 'checked' : '' }} {{ $estaCerrado ? 'disabled' : '' }}> 
                <label for="check_baño">Baño</label>
            </div>
            <div class="checklist-item">
                <input type="checkbox" id="check_corte" value="Corte de pelo" {{ in_array('Corte de pelo', $checklistGuardado) ? 'checked' : '' }} {{ $estaCerrado ? 'disabled' : '' }}> 
                <label for="check_corte">Corte de pelo</label>
            </div>
            <div class="checklist-item">
                <input type="checkbox" id="check_unas" value="Corte de uñas" {{ in_array('Corte de uñas', $checklistGuardado) ? 'checked' : '' }} {{ $estaCerrado ? 'disabled' : '' }}> 
                <label for="check_unas">Corte de uñas</label>
            </div>
            <div class="checklist-item">
                <input type="checkbox" id="check_oidos" value="Limpieza de oídos" {{ in_array('Limpieza de oídos', $checklistGuardado) ? 'checked' : '' }} {{ $estaCerrado ? 'disabled' : '' }}> 
                <label for="check_oidos">Limpieza de oídos</label>
            </div>
            <div class="checklist-item">
                <input type="checkbox" id="check_dientes" value="Cepillado dental" {{ in_array('Cepillado dental', $checklistGuardado) ? 'checked' : '' }} {{ $estaCerrado ? 'disabled' : '' }}> 
                <label for="check_dientes">Cepillado dental</label>
            </div>
            <div class="checklist-item">
                <input type="checkbox" id="check_perfume" value="Perfume" {{ in_array('Perfume', $checklistGuardado) ? 'checked' : '' }} {{ $estaCerrado ? 'disabled' : '' }}> 
                <label for="check_perfume">Perfume</label>
            </div>
            <textarea id="observaciones_checklist" rows="2" placeholder="Observaciones adicionales..." {{ $estaCerrado ? 'disabled' : '' }}>{{ $datosGuardados['observaciones'] ?? '' }}</textarea>
        </div>
        
        <!-- Fotos -->
        <div class="seccion">
            <h2>📸 Fotos</h2>
            <div class="fotos">
                <div class="foto-card">
                    <h3>📷 Antes</h3>
                    <div id="fotosAntes">
                        @foreach($fotosAntes as $foto)
                            <img src="{{ Storage::url($foto->url) }}" style="max-width: 150px; margin: 5px;">
                        @endforeach
                    </div>
                    @if(!$estaCerrado)
                    <input type="file" id="fotoAntes" accept="image/*" onchange="previsualizarFoto('antes')">
                    <button onclick="subirFoto('antes')" style="margin-top: 10px;">📷 Subir Foto Antes</button>
                    <div id="previewAntes" style="margin-top: 10px;"></div>
                    @endif
                </div>
                <div class="foto-card">
                    <h3>✨ Después</h3>
                    <div id="fotosDespues">
                        @foreach($fotosDespues as $foto)
                            <img src="{{ Storage::url($foto->url) }}" style="max-width: 150px; margin: 5px;">
                        @endforeach
                    </div>
                    @if(!$estaCerrado)
                    <input type="file" id="fotoDespues" accept="image/*" onchange="previsualizarFoto('despues')">
                    <button onclick="subirFoto('despues')" style="margin-top: 10px;">📷 Subir Foto Después</button>
                    <div id="previewDespues" style="margin-top: 10px;"></div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Recomendaciones -->
        <div class="seccion">
            <h2>💡 Recomendaciones para el Dueño</h2>
            <textarea id="recomendaciones" rows="3" placeholder="Recomendaciones para el cuidado de la mascota..." {{ $estaCerrado ? 'disabled' : '' }}>{{ $datosGuardados['recomendaciones'] ?? '' }}</textarea>
        </div>
        
        <!-- Botones -->
        <div class="seccion">
            @if(!$estaCerrado)
                <button class="btn btn-guardar" onclick="guardarProgreso()">💾 Guardar Progreso</button>
                <button class="btn btn-cerrar" onclick="cerrarServicio()">🔒 Cerrar Servicio</button>
            @else
                <div class="alerta-exito">
                    ✅ Servicio finalizado - No se pueden realizar más cambios
                </div>
            @endif
            <button class="btn btn-volver" onclick="window.location.href='/groomer/mis-citas?token={{ $token }}'">← Volver a Mis Citas</button>
        </div>
        
        <div id="resultado"></div>
    </div>

    <script>
    const token = '{{ $token }}';
    const citaId = '{{ $cita->id_cita }}';
    const estaCerrado = '{{ $cita->estado }}' === 'concluido';
    
    function previsualizarFoto(tipo) {
        const input = document.getElementById(tipo === 'antes' ? 'fotoAntes' : 'fotoDespues');
        const preview = document.getElementById(tipo === 'antes' ? 'previewAntes' : 'previewDespues');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 150px; border-radius: 8px;">';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function guardarProgreso() {
        const estadoIngreso = document.getElementById('estado_ingreso').value;
        const observaciones = document.getElementById('observaciones_checklist').value;
        const recomendaciones = document.getElementById('recomendaciones').value;
        
        const checklist = [];
        if (document.getElementById('check_baño').checked) checklist.push('Baño');
        if (document.getElementById('check_corte').checked) checklist.push('Corte de pelo');
        if (document.getElementById('check_unas').checked) checklist.push('Corte de uñas');
        if (document.getElementById('check_oidos').checked) checklist.push('Limpieza de oídos');
        if (document.getElementById('check_dientes').checked) checklist.push('Cepillado dental');
        if (document.getElementById('check_perfume').checked) checklist.push('Perfume');
        
        const btn = event.target;
        btn.disabled = true;
        btn.textContent = 'Guardando...';
        
        fetch('/groomer/guardar-progreso/' + citaId + '?token=' + token, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            },
            body: JSON.stringify({
                estado_ingreso: estadoIngreso,
                checklist: checklist,
                observaciones: observaciones,
                recomendaciones: recomendaciones
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('✅ Progreso guardado correctamente');
            } else {
                alert('❌ Error: ' + data.message);
            }
            btn.disabled = false;
            btn.textContent = '💾 Guardar Progreso';
        })
        .catch(error => {
            alert('❌ Error de conexión');
            btn.disabled = false;
            btn.textContent = '💾 Guardar Progreso';
        });
    }
    
    function subirFoto(tipo) {
        const fileInput = document.getElementById(tipo === 'antes' ? 'fotoAntes' : 'fotoDespues');
        const file = fileInput.files[0];
        
        if (!file) {
            alert('Selecciona una foto primero');
            return;
        }
        
        if (!file.type.startsWith('image/')) {
            alert('Solo se permiten archivos de imagen');
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            alert('La foto no debe superar los 5MB');
            return;
        }
        
        const btn = event.target;
        btn.disabled = true;
        btn.textContent = 'Subiendo...';
        
        const formData = new FormData();
        formData.append('foto', file);
        formData.append('tipo', tipo);
        formData.append('cita_id', citaId);
        
        fetch('/groomer/subir-foto-directo?token=' + token, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Foto subida correctamente');
                location.reload();
            } else {
                alert('❌ Error: ' + data.message);
                btn.disabled = false;
                btn.textContent = tipo === 'antes' ? '📷 Subir Foto Antes' : '📷 Subir Foto Después';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error de conexión: ' + error.message);
            btn.disabled = false;
            btn.textContent = tipo === 'antes' ? '📷 Subir Foto Antes' : '📷 Subir Foto Después';
        });
    }
    
    function cerrarServicio() {
        if (!confirm('¿Estás seguro de que deseas cerrar este servicio? No podrás editarlo después.')) {
            return;
        }
        
        const estadoIngreso = document.getElementById('estado_ingreso').value;
        const observaciones = document.getElementById('observaciones_checklist').value;
        const recomendaciones = document.getElementById('recomendaciones').value;
        
        if (!estadoIngreso.trim()) {
            alert('Por favor, completa el estado de ingreso antes de cerrar el servicio');
            return;
        }
        
        const checklist = [];
        if (document.getElementById('check_baño').checked) checklist.push('Baño');
        if (document.getElementById('check_corte').checked) checklist.push('Corte de pelo');
        if (document.getElementById('check_unas').checked) checklist.push('Corte de uñas');
        if (document.getElementById('check_oidos').checked) checklist.push('Limpieza de oídos');
        if (document.getElementById('check_dientes').checked) checklist.push('Cepillado dental');
        if (document.getElementById('check_perfume').checked) checklist.push('Perfume');
        
        const textoFinal = "=== ESTADO DE INGRESO ===\n" + estadoIngreso + 
                          "\n\n=== CHECKLIST REALIZADO ===\n" + (checklist.length > 0 ? checklist.join(', ') : 'Ninguno') + 
                          "\n\n=== OBSERVACIONES ===\n" + observaciones + 
                          "\n\n=== RECOMENDACIONES ===\n" + recomendaciones +
                          "\n\n=== FECHA CIERRE ===\n" + new Date().toLocaleString();
        
        const btn = event.target;
        btn.disabled = true;
        btn.textContent = 'Cerrando...';
        
        fetch('/groomer/cerrar-servicio/' + citaId + '?token=' + token, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                observaciones: textoFinal,
                checklist: checklist
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('✅ Servicio cerrado correctamente');
                window.location.href = '/groomer/mis-citas?token=' + token;
            } else {
                alert('❌ Error: ' + data.message);
                btn.disabled = false;
                btn.textContent = '🔒 Cerrar Servicio';
            }
        })
        .catch(error => {
            alert('❌ Error de conexión');
            btn.disabled = false;
            btn.textContent = '🔒 Cerrar Servicio';
        });
    }
    </script>
</body>
</html>