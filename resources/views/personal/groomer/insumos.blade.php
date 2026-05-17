<!DOCTYPE html>
<html>
<head>
    <title>Insumos - Groomer</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; }
        h1 { color: #4CAF50; }
        .btn { background: #607d8b; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; border: none; cursor: pointer; }
        .btn-volver { background: #607d8b; }
        .btn-refresh { background: #2196F3; }
        .seccion { background: #f5f5f5; padding: 15px; margin: 20px 0; border-radius: 8px; }
        h2 { color: #2196F3; margin-bottom: 15px; font-size: 1.3em; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #4CAF50; color: white; }
        tr:hover { background: #f1f1f1; }
        .stock-bajo { color: #f44336; font-weight: bold; }
        .stock-normal { color: #4CAF50; }
        .filtros { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; }
        .filtros input, .filtros select { padding: 8px; border: 1px solid #ddd; border-radius: 5px; flex: 1; }
        .card-insumo { display: inline-block; width: calc(33% - 20px); margin: 10px; background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); vertical-align: top; }
        .card-insumo h3 { color: #4CAF50; margin-bottom: 10px; }
        .card-insumo p { margin: 5px 0; }
        .stock { font-size: 1.2em; font-weight: bold; }
        .alert-bajo { background: #ffeb3b; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #ddd; }
        .tab { padding: 10px 20px; cursor: pointer; background: #f5f5f5; border-radius: 5px 5px 0 0; }
        .tab.active { background: #4CAF50; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .historial-item { margin: 10px 0; padding: 10px; background: white; border-radius: 5px; border-left: 4px solid #4CAF50; }
        .fecha { color: #666; font-size: 0.8em; }
        @media (max-width: 768px) {
            .card-insumo { width: calc(100% - 20px); }
            .header { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Mis Insumos</h1>
            <div>
                <button onclick="cargarDatos()" class="btn btn-refresh">🔄 Actualizar</button>
                <a href="/groomer/dashboard?token={{ $token }}" class="btn btn-volver">← Volver al Dashboard</a>
            </div>
        </div>

        @php $token = request()->query('token'); @endphp

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" onclick="mostrarTab('disponibles')">📋 Insumos Disponibles</div>
            <div class="tab" onclick="mostrarTab('historial')">📜 Mi Historial de Consumos</div>
        </div>

        <!-- Tab: Insumos Disponibles -->
        <div id="tab-disponibles" class="tab-content active">
            <div class="seccion">
                <h2>🔍 Buscar Insumos</h2>
                <div class="filtros">
                    <input type="text" id="searchInsumo" placeholder="Buscar por nombre..." onkeyup="filtrarInsumos()">
                    <select id="filtroStock" onchange="filtrarInsumos()">
                        <option value="todos">Todos los insumos</option>
                        <option value="stock_bajo">Stock bajo (mínimo)</option>
                        <option value="sin_stock">Sin stock</option>
                        <option value="con_stock">Con stock</option>
                    </select>
                </div>
            </div>

            <div id="insumosContainer" class="seccion">
                <div style="text-align: center; padding: 40px;">Cargando insumos...</div>
            </div>
        </div>

        <!-- Tab: Historial de Consumos -->
        <div id="tab-historial" class="tab-content">
            <div class="seccion">
                <h2>📅 Filtros</h2>
                <div class="filtros">
                    <input type="date" id="fechaInicio" placeholder="Fecha inicio">
                    <input type="date" id="fechaFin" placeholder="Fecha fin">
                    <button onclick="cargarHistorial()" class="btn btn-refresh">🔍 Filtrar</button>
                </div>
            </div>

            <div id="historialContainer" class="seccion">
                <div style="text-align: center; padding: 40px;">Cargando historial...</div>
            </div>
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
        let todosInsumos = [];

        function mostrarTab(tab) {
            // Cambiar tabs
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            
            if (tab === 'disponibles') {
                document.querySelector('.tab').classList.add('active');
                document.getElementById('tab-disponibles').classList.add('active');
                cargarInsumos();
            } else {
                document.querySelectorAll('.tab')[1].classList.add('active');
                document.getElementById('tab-historial').classList.add('active');
                cargarHistorial();
            }
        }

        function cargarDatos() {
            cargarInsumos();
            if (document.getElementById('tab-historial').classList.contains('active')) {
                cargarHistorial();
            }
        }

        function cargarInsumos() {
            fetch('/groomer/insumos-disponibles?token=' + token)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.insumos) {
                        todosInsumos = data.insumos;
                        mostrarInsumos(todosInsumos);
                    } else {
                        document.getElementById('insumosContainer').innerHTML = '<div style="text-align: center; padding: 40px;">❌ Error al cargar insumos</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('insumosContainer').innerHTML = '<div style="text-align: center; padding: 40px;">❌ Error de conexión</div>';
                });
        }

        function mostrarInsumos(insumos) {
            const container = document.getElementById('insumosContainer');
            
            if (!insumos || insumos.length === 0) {
                container.innerHTML = '<div style="text-align: center; padding: 40px;">📦 No hay insumos disponibles</div>';
                return;
            }

            // Verificar insumos con stock bajo
            const insumosBajos = insumos.filter(i => i.stock <= i.stock_minimo && i.stock > 0);
            
            let html = '';
            
            if (insumosBajos.length > 0) {
                html += '<div class="alert-bajo">';
                html += '⚠️ <strong>Alertas de stock bajo:</strong><br>';
                insumosBajos.forEach(i => {
                    html += `• ${i.nombre}: Stock ${i.stock} ${i.unidad_medida} (Mínimo: ${i.stock_minimo})<br>`;
                });
                html += '</div>';
            }
            
            html += '<div style="display: flex; flex-wrap: wrap;">';
            
            insumos.forEach(insumo => {
                let stockClass = insumo.stock === 0 ? 'stock-bajo' : (insumo.stock <= insumo.stock_minimo ? 'stock-bajo' : 'stock-normal');
                let stockText = insumo.stock === 0 ? '¡AGOTADO!' : `${insumo.stock} ${insumo.unidad_medida}`;
                
                html += `
                    <div class="card-insumo">
                        <h3>🧴 ${insumo.nombre}</h3>
                        <p><strong>Stock:</strong> <span class="stock ${stockClass}">${stockText}</span></p>
                        <p><strong>Stock mínimo:</strong> ${insumo.stock_minimo} ${insumo.unidad_medida}</p>
                        <p><strong>Unidad:</strong> ${insumo.unidad_medida}</p>
                    </div>
                `;
            });
            
            html += '</div>';
            container.innerHTML = html;
        }

        function filtrarInsumos() {
            const searchTerm = document.getElementById('searchInsumo').value.toLowerCase();
            const filtroStock = document.getElementById('filtroStock').value;
            
            let filtrados = todosInsumos.filter(insumo => {
                // Filtro por nombre
                if (searchTerm && !insumo.nombre.toLowerCase().includes(searchTerm)) {
                    return false;
                }
                
                // Filtro por stock
                if (filtroStock === 'stock_bajo') {
                    return insumo.stock <= insumo.stock_minimo && insumo.stock > 0;
                } else if (filtroStock === 'sin_stock') {
                    return insumo.stock === 0;
                } else if (filtroStock === 'con_stock') {
                    return insumo.stock > 0;
                }
                
                return true;
            });
            
            mostrarInsumos(filtrados);
        }

        function cargarHistorial() {
            const fechaInicio = document.getElementById('fechaInicio').value;
            const fechaFin = document.getElementById('fechaFin').value;
            
            let url = '/groomer/mis-consumos?token=' + token;
            if (fechaInicio) url += '&fecha_inicio=' + fechaInicio;
            if (fechaFin) url += '&fecha_fin=' + fechaFin;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.consumos) {
                        mostrarHistorial(data.consumos);
                    } else {
                        document.getElementById('historialContainer').innerHTML = '<div style="text-align: center; padding: 40px;">❌ Error al cargar historial</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('historialContainer').innerHTML = '<div style="text-align: center; padding: 40px;">❌ Error de conexión</div>';
                });
        }

        function mostrarHistorial(consumos) {
            const container = document.getElementById('historialContainer');
            
            if (!consumos || consumos.length === 0) {
                container.innerHTML = '<div style="text-align: center; padding: 40px;">📜 No hay registros de consumo</div>';
                return;
            }
            
            let html = '<table><thead><tr>';
            html += '<th>Fecha</th>';
            html += '<th>Mascota</th>';
            html += '<th>Servicio</th>';
            html += '<th>Insumo</th>';
            html += '<th>Cantidad</th>';
            html += '<th>Unidad</th>';
            html += '</tr></thead><tbody>';
            
            consumos.forEach(consumo => {
                html += '<tr>';
                html += `<td>${consumo.fecha || '-'}</td>`;
                html += `<td>${consumo.mascota_nombre || '-'}</td>`;
                html += `<td>${consumo.servicio_nombre || '-'}</td>`;
                html += `<td>${consumo.insumo_nombre}</td>`;
                html += `<td>${consumo.cantidad_usada}</td>`;
                html += `<td>${consumo.unidad_medida}</td>`;
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        // Cargar datos al iniciar
        cargarInsumos();
    </script>
</body>
</html>