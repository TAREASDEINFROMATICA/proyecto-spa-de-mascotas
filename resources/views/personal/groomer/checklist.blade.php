<!DOCTYPE html>
<html>
<head>
    <title>Ficha Checklist - Groomer</title>
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
            padding: 40px 20px; 
        }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 35px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card-header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 20px 25px; }
        .card-header h2 { display: flex; align-items: center; gap: 10px; font-size: 22px; }
        .card-header h2 i { font-size: 28px; }
        .content { padding: 25px; }
        
        .cita-seleccion { margin-bottom: 25px; }
        .cita-seleccion select { width: 100%; padding: 14px; border: 2px solid #e2e8f0; border-radius: 14px; font-size: 16px; font-family: 'Inter', sans-serif; }
        
        .info-cita { background: #e8f5e9; padding: 20px; border-radius: 16px; margin-bottom: 25px; border-left: 4px solid #4CAF50; }
        .info-cita h3 { margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .info-cita .info-row { margin: 10px 0; display: flex; gap: 15px; flex-wrap: wrap; }
        .info-cita .info-row i { width: 24px; color: #4CAF50; }
        
        .progreso { background: #f5f5f5; border-radius: 12px; padding: 15px; margin-bottom: 20px; }
        .barra-progreso { height: 12px; background: #e0e0e0; border-radius: 6px; overflow: hidden; margin-top: 10px; }
        .barra-progreso-fill { height: 100%; background: linear-gradient(90deg, #4CAF50, #45a049); width: 0%; transition: width 0.3s; border-radius: 6px; }
        
        .checklist-item { background: #f8f9fa; border-radius: 16px; padding: 15px 20px; margin-bottom: 12px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap; transition: 0.2s; }
        .checklist-item.completado { background: #e8f5e9; border-left: 4px solid #4CAF50; }
        .checklist-item .checkbox { width: 30px; height: 30px; cursor: pointer; accent-color: #4CAF50; }
        .checklist-item .info { flex: 1; }
        .checklist-item .info strong { font-size: 16px; display: block; }
        .checklist-item .info p { font-size: 13px; color: #666; margin-top: 4px; }
        .checklist-item .observacion { width: 100%; margin-top: 10px; display: none; }
        .checklist-item .observacion textarea { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Inter', sans-serif; resize: vertical; }
        
        .btn-guardar { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; border: none; padding: 14px 24px; border-radius: 14px; font-weight: 600; cursor: pointer; width: 100%; font-size: 16px; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-guardar:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(76,175,80,0.3); }
        .btn-completar { background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); color: white; border: none; padding: 14px 24px; border-radius: 14px; font-weight: 600; cursor: pointer; width: 100%; font-size: 16px; margin-top: 15px; transition: 0.3s; }
        .btn-completar:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(33,150,243,0.3); }
        
        .btn-back { background: #607d8b; color: white; padding: 12px 24px; border-radius: 14px; text-decoration: none; display: inline-block; margin-top: 20px; text-align: center; }
        
        .empty-state { text-align: center; padding: 50px; color: #64748b; }
        .empty-state i { font-size: 60px; margin-bottom: 15px; opacity: 0.5; }
        
        @media (max-width: 640px) { .content { padding: 20px; } .checklist-item { flex-direction: column; align-items: flex-start; } }
    </style>
</head>
<body>
    @php $token = request()->query('token'); @endphp
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-clipboard-list"></i> Ficha Checklist</h2>
            </div>
            
            <div class="content">
                @if($citas->count() > 0)
                    <div class="cita-seleccion">
                        <select id="selectCita" style="width: 100%;">
                            <option value="">🔍 Seleccionar cita activa...</option>
                            @foreach($citas as $c)
                                <option value="{{ $c->id_cita }}">
                                    #{{ $c->id_cita }} - {{ $c->mascota->nombre }} - {{ $c->servicio->nombre }} 
                                    ({{ \Carbon\Carbon::parse($c->fecha)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="checklistContainer" style="display: none;">
                        <div id="infoCita" class="info-cita"></div>
                        <div id="progresoContainer" class="progreso">
                            <div>📊 Progreso del checklist</div>
                            <div class="barra-progreso">
                                <div id="barraProgreso" class="barra-progreso-fill"></div>
                            </div>
                            <div id="textoProgreso" style="margin-top: 8px; font-size: 13px;">0% completado</div>
                        </div>
                        <div id="itemsList"></div>
                        <button id="btnCompletar" class="btn-completar" style="display: none;">
                            <i class="fas fa-check-circle"></i> Marcar Checklist como Completado
                        </button>
                        <button id="btnGuardar" class="btn-guardar" style="display: none;">
                            <i class="fas fa-save"></i> Guardar Progreso
                        </button>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-check"></i>
                        <h3>No hay citas activas</h3>
                        <p>No tienes citas programadas en este momento</p>
                    </div>
                @endif
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="/groomer/dashboard?token={{ $token }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const token = '{{ $token }}';
        let currentCitaId = null;
        
        $('#selectCita').change(function() {
            const citaId = $(this).val();
            if (!citaId) {
                $('#checklistContainer').hide();
                return;
            }
            currentCitaId = citaId;
            cargarChecklist(citaId);
            $('#checklistContainer').show();
        });
        
        function cargarChecklist(citaId) {
            $('#itemsList').html('<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin"></i> Cargando checklist...</div>');
            $('#btnGuardar').hide();
            $('#btnCompletar').hide();
            
            $.ajax({
                url: '/groomer/checklist/' + citaId + '/items',
                method: 'GET',
                data: { token: token },
                success: function(data) {
                    mostrarInfoCita(data.cita);
                    mostrarItems(data.items, data.progreso);
                    actualizarProgreso(data.items, data.progreso);
                },
                error: function(xhr) {
                    $('#itemsList').html('<div style="color: red; text-align: center;">Error al cargar el checklist</div>');
                }
            });
        }
        
        function mostrarInfoCita(cita) {
            const html = `
                <h3><i class="fas fa-info-circle"></i> Información de la cita</h3>
                <div class="info-row">
                    <i class="fas fa-dog"></i> <strong>Mascota:</strong> ${cita.mascota.nombre}
                </div>
                <div class="info-row">
                    <i class="fas fa-cut"></i> <strong>Servicio:</strong> ${cita.servicio.nombre}
                </div>
                <div class="info-row">
                    <i class="fas fa-calendar"></i> <strong>Fecha:</strong> ${cita.fecha}
                </div>
                <div class="info-row">
                    <i class="fas fa-clock"></i> <strong>Hora:</strong> ${cita.hora_inicio} - ${cita.hora_fin}
                </div>
            `;
            $('#infoCita').html(html);
        }
        
        function mostrarItems(items, progreso) {
            if (!items || items.length === 0) {
                $('#itemsList').html('<div style="text-align: center; padding: 40px; color: #ff9800;"><i class="fas fa-info-circle"></i> No hay items configurados para este servicio</div>');
                return;
            }
            
            let html = '';
            items.forEach(item => {
                const itemProgreso = progreso[item.id_item];
                const realizado = itemProgreso ? itemProgreso.realizado : false;
                const observacion = itemProgreso ? itemProgreso.observacion || '' : '';
                const completadoClass = realizado ? 'completado' : '';
                
                html += `
                    <div class="checklist-item ${completadoClass}" data-item-id="${item.id_item}">
                        <input type="checkbox" class="checkbox" data-item-id="${item.id_item}" ${realizado ? 'checked' : ''}>
                        <div class="info">
                            <strong>${item.nombre}</strong>
                            ${item.requiere_observacion ? '<p><small><i class="fas fa-comment"></i> Requiere observación</small></p>' : ''}
                        </div>
                        <div class="observacion" style="${item.requiere_observacion ? 'display: block;' : 'display: none;'}">
                            <textarea class="obs-textarea" data-item-id="${item.id_item}" placeholder="Agregar observación...">${observacion}</textarea>
                        </div>
                    </div>
                `;
            });
            
            $('#itemsList').html(html);
            $('#btnGuardar').show();
            
            // Verificar si todos los items están completados
            verificarCompletadoTotal();
        }
        
        function actualizarProgreso(items, progreso) {
            const total = items.length;
            let completados = 0;
            
            items.forEach(item => {
                if (progreso[item.id_item] && progreso[item.id_item].realizado) {
                    completados++;
                }
            });
            
            const porcentaje = total > 0 ? Math.round((completados / total) * 100) : 0;
            $('#barraProgreso').css('width', porcentaje + '%');
            $('#textoProgreso').html(`${porcentaje}% completado (${completados}/${total} tareas)`);
        }
        
        function verificarCompletadoTotal() {
            const total = $('.checklist-item').length;
            const completados = $('.checkbox:checked').length;
            
            if (total > 0 && completados === total) {
                $('#btnCompletar').show();
            } else {
                $('#btnCompletar').hide();
            }
        }
        
        $(document).on('change', '.checkbox', function() {
            const itemId = $(this).data('item-id');
            const realizado = $(this).is(':checked');
            const parentDiv = $(this).closest('.checklist-item');
            const observacion = parentDiv.find('.obs-textarea').val() || '';
            
            if (realizado) {
                parentDiv.addClass('completado');
            } else {
                parentDiv.removeClass('completado');
            }
            
            // Guardar automáticamente
            guardarItem(itemId, realizado, observacion);
        });
        
        $(document).on('blur', '.obs-textarea', function() {
            const parentDiv = $(this).closest('.checklist-item');
            const itemId = parentDiv.find('.checkbox').data('item-id');
            const realizado = parentDiv.find('.checkbox').is(':checked');
            const observacion = $(this).val() || '';
            
            guardarItem(itemId, realizado, observacion);
        });
        
        function guardarItem(itemId, realizado, observacion) {
            $.ajax({
                url: '/groomer/checklist/' + currentCitaId + '/guardar',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: {
                    id_item: itemId,
                    realizado: realizado ? 1 : 0,
                    observacion: observacion,
                    token: token,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    // Actualizar contador de progreso
                    const total = $('.checklist-item').length;
                    const completados = $('.checkbox:checked').length;
                    const porcentaje = total > 0 ? Math.round((completados / total) * 100) : 0;
                    $('#barraProgreso').css('width', porcentaje + '%');
                    $('#textoProgreso').html(`${porcentaje}% completado (${completados}/${total} tareas)`);
                    verificarCompletadoTotal();
                }
            });
        }
        
        $('#btnCompletar').click(function() {
            if (!confirm('¿Marcar este checklist como completado? El servicio quedará listo para cerrar.')) return;
            
            $.ajax({
                url: '/groomer/checklist/' + currentCitaId + '/completar',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { token: token, _token: '{{ csrf_token() }}' },
                success: function(res) {
                    alert('✅ Checklist completado. El servicio está listo para cerrar.');
                    location.reload();
                }
            });
        });
    </script>
</body>
</html>