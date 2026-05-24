<!DOCTYPE html>
<html>
<head>
    <title>Admin - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; display: flex; align-items: center; justify-content: center; gap: 12px; }
        .header p { opacity: 0.9; margin-top: 8px; font-size: 14px; }
        .content { padding: 30px; }
        
        .info-card { background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); padding: 20px; border-radius: 16px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .info-card i { font-size: 48px; color: #2E7D32; }
        .info-text { flex: 1; }
        .info-text strong { font-size: 18px; color: #1B5E20; }
        .info-text p { color: #555; margin-top: 5px; }
        
        .notif-badge { background: #f44336; color: white; border-radius: 50%; padding: 2px 8px; font-size: 11px; font-weight: 600; margin-left: 8px; }
        .notif-button { background: #4CAF50; color: white; padding: 10px 15px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .notif-button:hover { background: #45a049; transform: translateY(-2px); }
        
        h2 { font-size: 18px; color: #333; margin: 25px 0 15px 0; display: flex; align-items: center; gap: 10px; padding-bottom: 8px; border-bottom: 2px solid #e0e0e0; }
        h2 i { color: #4CAF50; font-size: 20px; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; margin-bottom: 10px; }
        .menu-item { background: #f8f9fa; border-radius: 12px; transition: all 0.3s ease; overflow: hidden; }
        .menu-item:hover { background: #e8f5e9; transform: translateX(5px); }
        .menu-item a { display: flex; align-items: center; gap: 12px; padding: 14px 18px; text-decoration: none; color: #333; font-weight: 500; transition: all 0.3s; }
        .menu-item a i { width: 28px; font-size: 18px; color: #4CAF50; }
        .menu-item a:hover { color: #4CAF50; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s; border-left: 4px solid; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .icon { font-size: 32px; margin-bottom: 10px; }
        .stat-card .value { font-size: 28px; font-weight: 700; }
        .stat-card .label { color: #666; font-size: 14px; margin-top: 5px; }
        
        .btn-logout { background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white; border: none; padding: 14px 24px; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 20px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-logout:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(244,67,54,0.3); }
        
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 20px; width: 450px; max-width: 90%; padding: 25px; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .modal-content h3 { margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .modal-content input { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 12px; margin: 10px 0; font-family: 'Inter', sans-serif; }
        .modal-content input:focus { outline: none; border-color: #4CAF50; }
        .password-container { position: relative; }
        .toggle-password { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999; }
        .strength-meter { height: 6px; background: #e0e0e0; border-radius: 3px; margin: 10px 0; overflow: hidden; }
        .strength-meter-fill { height: 100%; width: 0%; transition: width 0.3s; }
        .weak { background: #f44336; }
        .medium { background: #ff9800; }
        .strong { background: #4CAF50; }
        .requirement { font-size: 11px; margin: 5px 0; display: inline-block; width: 48%; }
        .requirement.valid { color: #4CAF50; }
        .requirement.invalid { color: #f44336; }
        .modal-buttons { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .btn-confirm { background: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
        .btn-cancel-modal { background: #607d8b; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
        .close { float: right; font-size: 24px; cursor: pointer; color: #999; }
        .close:hover { color: #333; }
        .mensaje-exito { background: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-top: 10px; text-align: center; }
        .mensaje-error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-top: 10px; text-align: center; }
        
        @media (max-width: 640px) { .menu-grid { grid-template-columns: 1fr; } .info-card { flex-direction: column; text-align: center; } .stats-grid { grid-template-columns: repeat(2, 1fr); } .requirement { width: 100%; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shield-alt"></i> Panel de Administrador</h1>
            <p>Gestión completa del Spa de Mascotas</p>
        </div>
        
        <div class="content">
            <!-- ========================================================= -->
            <!-- INFO USUARIO + NOTIFICACIONES -->
            <!-- ========================================================= -->
            <div class="info-card">
                <i class="fas fa-user-circle"></i>
                <div class="info-text">
                    <strong id="userName">Cargando...</strong>
                    <p id="userEmail">Cargando datos del usuario...</p>
                    <p id="userRol" style="margin-top: 5px;"></p>
                </div>
                <div>
                    <a href="#" id="enlaceNotificaciones" class="notif-button">
                        <i class="fas fa-bell"></i> Notificaciones
                        <span id="notifCount" class="notif-badge" style="display: none;">0</span>
                    </a>
                </div>
            </div>
            
            <!-- ========================================================= -->
            <!-- ESTADÍSTICAS - TARJETAS -->
            <!-- ========================================================= -->
            <div class="stats-grid">
                <div class="stat-card" style="border-left-color: #4CAF50;">
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <div class="value">Bs {{ number_format($ventasHoy ?? 0, 2) }}</div>
                    <div class="label">Ventas de hoy</div>
                </div>
                <div class="stat-card" style="border-left-color: #4CAF50;">
                    <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="value">Bs {{ number_format($ventasMes ?? 0, 2) }}</div>
                    <div class="label">Ventas del mes</div>
                </div>
                <div class="stat-card" style="border-left-color: #4CAF50;">
                    <div class="icon"><i class="fas fa-chart-bar"></i></div>
                    <div class="value">Bs {{ number_format($ventasTotales ?? 0, 2) }}</div>
                    <div class="label">Ventas totales</div>
                </div>
                <div class="stat-card" style="border-left-color: #2196F3;">
                    <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="value">{{ $citasHoy ?? 0 }}</div>
                    <div class="label">Citas hoy</div>
                </div>
                <div class="stat-card" style="border-left-color: #2196F3;">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <div class="value">{{ $citasPendientes ?? 0 }}</div>
                    <div class="label">Citas pendientes</div>
                </div>
                <div class="stat-card" style="border-left-color: #2196F3;">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <div class="value">{{ $citasConfirmadas ?? 0 }}</div>
                    <div class="label">Citas confirmadas</div>
                </div>
                <div class="stat-card" style="border-left-color: #2196F3;">
                    <div class="icon"><i class="fas fa-check-double"></i></div>
                    <div class="value">{{ $citasConcluidas ?? 0 }}</div>
                    <div class="label">Citas concluidas</div>
                </div>
                <div class="stat-card" style="border-left-color: #ff9800;">
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="value">{{ ($productosStockBajo ?? 0) + ($insumosStockBajo ?? 0) }}</div>
                    <div class="label">Alertas de stock bajo</div>
                </div>
            </div>
            
            <!-- ========================================================= -->
            <!-- MENÚ PRINCIPAL -->
            <!-- ========================================================= -->
            <h2><i class="fas fa-tachometer-alt"></i> Gestión Principal</h2>
<div class="menu-grid">
    <div class="menu-item"><a href="#" id="enlaceEmpleados"><i class="fas fa-users"></i> Gestionar Empleados</a></div>
    <div class="menu-item"><a href="#" id="enlaceServicios"><i class="fas fa-cut"></i> Gestionar Servicios</a></div>
    <div class="menu-item"><a href="#" id="enlaceAgenda"><i class="fas fa-calendar-alt"></i> Agenda Maestra</a></div>
    <div class="menu-item"><a href="#" id="enlaceCitas"><i class="fas fa-calendar-check"></i> Ver todas las citas</a></div>
    <!-- 🔽 NUEVO BOTÓN DE DÍAS NO LABORABLES 🔽 -->
    <div class="menu-item"><a href="#" id="enlaceDiasNoLaborables"><i class="fas fa-calendar-times"></i> Días No Laborables</a></div>
</div>
            
            <h2><i class="fas fa-chart-line"></i> Ventas y Finanzas</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceReportes"><i class="fas fa-file-alt"></i> Reportes financieros</a></div>
                <div class="menu-item"><a href="#" id="enlaceVentas"><i class="fas fa-shopping-cart"></i> Gestión de Ventas</a></div>
            </div>
            
            <h2><i class="fas fa-boxes"></i> Inventario</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceInsumos"><i class="fas fa-flask"></i> Gestión de Insumos</a></div>
                <div class="menu-item"><a href="#" id="enlaceProductos"><i class="fas fa-store"></i> Gestión de Productos</a></div>
                <div class="menu-item"><a href="#" id="enlaceCategorias"><i class="fas fa-folder"></i> Gestión de Categorías</a></div>
                <div class="menu-item"><a href="#" id="enlaceAlertasStock"><i class="fas fa-exclamation-triangle"></i> Alertas de Stock Bajo</a></div>
            </div>
            
            <h2><i class="fas fa-user-friends"></i> Gestión de Usuarios</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceClientes"><i class="fas fa-user"></i> Gestionar Clientes</a></div>
                <div class="menu-item"><a href="#" id="enlaceMascotas"><i class="fas fa-dog"></i> Todas las Mascotas</a></div>
            </div>
            
            <h2><i class="fas fa-lock"></i> Seguridad</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlace2fa"><i class="fas fa-key"></i> Configurar 2FA</a></div>
                <div class="menu-item"><a href="#" id="enlaceLogs"><i class="fas fa-history"></i> Ver Logs de Auditoría</a></div>
            </div>
            
            <button onclick="logout()" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </div>
    </div>

    <!-- Modal para cambiar contraseña -->
    <div id="modalPassword" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h3><i class="fas fa-lock"></i> Cambiar Contraseña</h3>
            <form id="cambiarPasswordForm">
                <div class="password-container">
                    <input type="password" id="contrasena_actual" placeholder="Contraseña actual" required>
                    <span class="toggle-password" onclick="togglePassword('contrasena_actual')">👁️</span>
                </div>
                <div class="password-container">
                    <input type="password" id="contrasena_nueva" placeholder="Nueva contraseña" required>
                    <span class="toggle-password" onclick="togglePassword('contrasena_nueva')">👁️</span>
                </div>
                <div class="strength-meter"><div class="strength-meter-fill" id="strengthFill"></div></div>
                <div id="strengthText" style="font-size: 12px; margin-bottom: 10px;"></div>
                <div id="requisitos">
                    <div class="requirement invalid" id="req-length">❌ Mínimo 8 caracteres</div>
                    <div class="requirement invalid" id="req-upper">❌ Al menos una mayúscula</div>
                    <div class="requirement invalid" id="req-lower">❌ Al menos una minúscula</div>
                    <div class="requirement invalid" id="req-number">❌ Al menos un número</div>
                    <div class="requirement invalid" id="req-symbol">❌ Al menos un símbolo (@$!%*#?&)</div>
                </div>
                <div class="password-container">
                    <input type="password" id="contrasena_nueva_confirmation" placeholder="Confirmar contraseña" required>
                    <span class="toggle-password" onclick="togglePassword('contrasena_nueva_confirmation')">👁️</span>
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="btn-confirm">✅ Cambiar</button>
                    <button type="button" onclick="cerrarModal()" class="btn-cancel-modal">Cancelar</button>
                </div>
            </form>
            <div id="passwordResultado"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const token = localStorage.getItem('token');
        
        if (!token) {
            window.location.href = '/';
        }
        
        // Función para crear enlaces con token
        function crearEnlace(id, url) {
            const enlace = document.getElementById(id);
            if (enlace) {
                enlace.href = url + '?token=' + token;
            }
        }
        
        // =========================================================
        // NOTIFICACIONES - CONTADOR
        // =========================================================
        function actualizarNotificaciones() {
            fetch('/notificaciones/count?token=' + token)
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('notifCount');
                    if (badge && data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else if (badge) {
                        badge.style.display = 'none';
                    }
                })
                .catch(err => console.log('Error:', err));
        }
        
        setInterval(actualizarNotificaciones, 30000);
        actualizarNotificaciones();
        
        // =========================================================
        // GESTIÓN PRINCIPAL
        // =========================================================
        crearEnlace('enlaceEmpleados', '/admin/empleados');
        crearEnlace('enlaceServicios', '/admin/servicios');
        crearEnlace('enlaceAgenda', '/admin/agenda');
        crearEnlace('enlaceCitas', '/admin/citas/todas');
        crearEnlace('enlaceDiasNoLaborables', '/admin/dias-no-laborables'); 
        
        // =========================================================
        // VENTAS Y FINANZAS
        // =========================================================
        crearEnlace('enlaceReportes', '/admin/reportes-financieros');
        crearEnlace('enlaceVentas', '/admin/ventas');
        
        // =========================================================
        // INVENTARIO
        // =========================================================
        crearEnlace('enlaceInsumos', '/admin/insumos');
        crearEnlace('enlaceProductos', '/admin/productos');
        crearEnlace('enlaceCategorias', '/admin/categorias');
        crearEnlace('enlaceAlertasStock', '/admin/alertas-stock');
        
        // =========================================================
        // GESTIÓN DE USUARIOS
        // =========================================================
        crearEnlace('enlaceClientes', '/admin/clientes');
        crearEnlace('enlaceMascotas', '/admin/mascotas');
        
        // =========================================================
        // SEGURIDAD
        // =========================================================
        crearEnlace('enlace2fa', '/admin/configurar-2fa');
        crearEnlace('enlaceLogs', '/admin/logs');
        
        // =========================================================
        // NOTIFICACIONES
        // =========================================================
        crearEnlace('enlaceNotificaciones', '/mis-notificaciones');
        
        // =========================================================
        // MODAL CAMBIAR CONTRASEÑA
        // =========================================================
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function checkPasswordStrength(password) {
            const checks = {
                length: password.length >= 8,
                upper: /[A-Z]/.test(password),
                lower: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                symbol: /[@$!%*#?&]/.test(password)
            };
            document.getElementById('req-length').innerHTML = (checks.length ? '✅' : '❌') + ' Mínimo 8 caracteres';
            document.getElementById('req-upper').innerHTML = (checks.upper ? '✅' : '❌') + ' Al menos una mayúscula';
            document.getElementById('req-lower').innerHTML = (checks.lower ? '✅' : '❌') + ' Al menos una minúscula';
            document.getElementById('req-number').innerHTML = (checks.number ? '✅' : '❌') + ' Al menos un número';
            document.getElementById('req-symbol').innerHTML = (checks.symbol ? '✅' : '❌') + ' Al menos un símbolo';
            
            let strength = 0;
            if (checks.length) strength++;
            if (checks.upper && checks.lower) strength++;
            if (checks.number) strength++;
            if (checks.symbol) strength++;
            
            const fill = document.getElementById('strengthFill');
            const text = document.getElementById('strengthText');
            fill.style.width = (strength * 25) + '%';
            if (strength <= 2) {
                fill.className = 'strength-meter-fill weak';
                text.innerHTML = '🔴 Contraseña débil';
            } else if (strength === 3) {
                fill.className = 'strength-meter-fill medium';
                text.innerHTML = '🟡 Contraseña media';
            } else {
                fill.className = 'strength-meter-fill strong';
                text.innerHTML = '🟢 Contraseña fuerte';
            }
            return checks.length && checks.upper && checks.lower && checks.number && checks.symbol;
        }
        
        document.getElementById('contrasena_nueva').addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        function abrirModal() { document.getElementById('modalPassword').style.display = 'flex'; }
        function cerrarModal() { 
            document.getElementById('modalPassword').style.display = 'none';
            document.getElementById('cambiarPasswordForm').reset();
            document.getElementById('passwordResultado').innerHTML = '';
            document.getElementById('strengthFill').style.width = '0%';
            document.getElementById('strengthText').innerHTML = '';
            document.querySelectorAll('.requirement').forEach(r => {
                r.classList.remove('valid');
                r.classList.add('invalid');
                r.innerHTML = r.innerHTML.replace('✅', '❌');
            });
        }
        
        // Cargar datos del usuario
        fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(res => res.json())
        .then(data => {
            if (data.user) {
                document.getElementById('userName').innerHTML = `👋 Bienvenido, ${data.user.nombres} ${data.user.apellidos}`;
                document.getElementById('userEmail').innerHTML = `📧 ${data.user.correo}`;
                document.getElementById('userRol').innerHTML = `<i class="fas fa-tag"></i> Rol: <strong>${data.user.rol.nombre}</strong>`;
            } else {
                document.querySelector('.info-card').innerHTML = '<span style="color: red;">❌ Error al cargar datos</span>';
            }
        })
        .catch(() => {
            window.location.href = '/';
        });

        function logout() {
            fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                }
            }).finally(() => {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = '/';
            });
        }
        
        // Cambiar contraseña
        $('#cambiarPasswordForm').on('submit', function(e) {
            e.preventDefault();
            const nueva = $('#contrasena_nueva').val();
            const conf = $('#contrasena_nueva_confirmation').val();
            if (!checkPasswordStrength(nueva)) {
                $('#passwordResultado').html('<div class="mensaje-error">❌ Contraseña no cumple requisitos</div>');
                return;
            }
            if (nueva !== conf) {
                $('#passwordResultado').html('<div class="mensaje-error">❌ Las contraseñas no coinciden</div>');
                return;
            }
            $.ajax({
                url: '/cambiar-contrasena',
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                data: JSON.stringify({
                    contrasena_actual: $('#contrasena_actual').val(),
                    contrasena_nueva: nueva,
                    contrasena_nueva_confirmation: conf
                }),
                success: function(res) {
                    $('#passwordResultado').html('<div class="mensaje-exito">✅ ' + res.message + '</div>');
                    setTimeout(cerrarModal, 2000);
                },
                error: function(xhr) {
                    $('#passwordResultado').html('<div class="mensaje-error">❌ ' + (xhr.responseJSON?.message || 'Error') + '</div>');
                }
            });
        });
        
        // =========================================================
        // SESIÓN POR INACTIVIDAD
        // =========================================================
        let tiempoInactividad;
        const TIEMPO_LIMITE = 30 * 60 * 1000; // 30 minutos
        
        function cerrarSesionPorInactividad() {
            const tokenActual = localStorage.getItem('token');
            if (tokenActual) {
                fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + tokenActual,
                        'Content-Type': 'application/json'
                    }
                }).finally(() => {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    window.location.href = '/';
                });
            } else {
                window.location.href = '/';
            }
        }
        
        function reiniciarTiempo() {
            clearTimeout(tiempoInactividad);
            tiempoInactividad = setTimeout(cerrarSesionPorInactividad, TIEMPO_LIMITE);
        }
        
        window.onload = reiniciarTiempo;
        document.onmousemove = reiniciarTiempo;
        document.onkeydown = reiniciarTiempo;
        document.onclick = reiniciarTiempo;
        document.onscroll = reiniciarTiempo;
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('modalPassword');
            if (event.target == modal) cerrarModal();
        }
    </script>
</body>
</html>