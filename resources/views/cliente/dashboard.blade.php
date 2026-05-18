<!DOCTYPE html>
<html>
<head>
    <title>Cliente - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; display: flex; align-items: center; justify-content: center; gap: 12px; }
        .header p { opacity: 0.9; margin-top: 8px; font-size: 14px; }
        .content { padding: 30px; }
        
        .info-card { background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); padding: 20px; border-radius: 16px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .info-card i { font-size: 48px; color: #F57C00; }
        .info-text { flex: 1; }
        .info-text strong { font-size: 18px; color: #E65100; }
        .info-text p { color: #555; margin-top: 5px; }
        
        .notif-badge { background: #f44336; color: white; border-radius: 50%; padding: 2px 8px; font-size: 11px; font-weight: 600; margin-left: 8px; }
        
        h2 { font-size: 18px; color: #333; margin: 25px 0 15px 0; display: flex; align-items: center; gap: 10px; padding-bottom: 8px; border-bottom: 2px solid #e0e0e0; }
        h2 i { color: #FF9800; font-size: 20px; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; margin-bottom: 10px; }
        .menu-item { background: #f8f9fa; border-radius: 12px; transition: all 0.3s ease; overflow: hidden; }
        .menu-item:hover { background: #fff3e0; transform: translateX(5px); }
        .menu-item a { display: flex; align-items: center; gap: 12px; padding: 14px 18px; text-decoration: none; color: #333; font-weight: 500; transition: all 0.3s; }
        .menu-item a i { width: 28px; font-size: 18px; color: #FF9800; }
        .menu-item a:hover { color: #FF9800; }
        
        .cart-badge { background: #f44336; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold; margin-left: 8px; display: inline-block; }
        .notif-button { background: #FF9800; color: white; padding: 10px 15px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .notif-button:hover { background: #F57C00; transform: translateY(-2px); }
        
        .btn-logout { background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white; border: none; padding: 14px 24px; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 20px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-logout:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(244,67,54,0.3); }
        
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 20px; width: 450px; max-width: 90%; padding: 25px; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .modal-content h3 { margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .modal-content input { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 12px; margin: 10px 0; font-family: 'Inter', sans-serif; }
        .modal-content input:focus { outline: none; border-color: #FF9800; }
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
        
        @media (max-width: 640px) { .menu-grid { grid-template-columns: 1fr; } .info-card { flex-direction: column; text-align: center; } .requirement { width: 100%; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-paw"></i> Panel del Cliente</h1>
            <p>Gestiona tus mascotas, citas y compras</p>
        </div>
        
        <div class="content">
            <div class="info-card">
                <i class="fas fa-user-circle"></i>
                <div class="info-text">
                    <strong id="userName">Cargando...</strong>
                    <p id="userEmail">Cargando datos del usuario...</p>
                </div>
                <div>
                    <a href="#" id="enlaceNotificaciones" class="notif-button">
                        <i class="fas fa-bell"></i> Notificaciones
                        <span id="notifCount" class="notif-badge" style="display: none;">0</span>
                    </a>
                </div>
            </div>
            
            <h2><i class="fas fa-calendar-alt"></i> Citas</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceSolicitarCita"><i class="fas fa-plus-circle"></i> Solicitar Cita</a></div>
                <div class="menu-item"><a href="#" id="enlaceMisCitas"><i class="fas fa-calendar-check"></i> Mis Citas</a></div>
                <div class="menu-item"><a href="#" id="enlaceHistorialServicios"><i class="fas fa-history"></i> Historial de Servicios</a></div>
            </div>
            
            <h2><i class="fas fa-dog"></i> Mis Mascotas</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceMascotas"><i class="fas fa-paw"></i> Ver mis mascotas</a></div>
                <div class="menu-item"><a href="#" id="enlaceRegistrarMascota"><i class="fas fa-plus"></i> Registrar Nueva Mascota</a></div>
            </div>
            
            <h2><i class="fas fa-star"></i> Calificaciones</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceCalificar"><i class="fas fa-star"></i> Calificar Servicios</a></div>
            </div>
            
            <h2><i class="fas fa-store"></i> Tienda</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceTienda"><i class="fas fa-shopping-bag"></i> Tienda</a></div>
                <div class="menu-item"><a href="#" id="enlaceCarrito"><i class="fas fa-shopping-cart"></i> Mi Carrito <span id="carritoCount" class="cart-badge">0</span></a></div>
                <div class="menu-item"><a href="#" id="enlaceMisCompras"><i class="fas fa-receipt"></i> Mis Compras</a></div>
            </div>
            
            <h2><i class="fas fa-user-cog"></i> Mi Cuenta</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlacePerfil"><i class="fas fa-user-edit"></i> Actualizar mis datos</a></div>
                <div class="menu-item"><a href="#" id="enlaceCambiarPassword"><i class="fas fa-key"></i> Cambiar Contraseña</a></div>
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
        
        // Función para actualizar contador del carrito
        function actualizarCarritoCount() {
            const carrito = localStorage.getItem('carrito');
            const items = carrito ? JSON.parse(carrito) : [];
            const totalItems = items.reduce((sum, item) => sum + item.cantidad, 0);
            const badge = document.getElementById('carritoCount');
            if (badge) {
                badge.textContent = totalItems;
                badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
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
        // CITAS
        // =========================================================
        crearEnlace('enlaceSolicitarCita', '/cliente/solicitar-cita');
        crearEnlace('enlaceMisCitas', '/cliente/mis-citas');
        crearEnlace('enlaceHistorialServicios', '/cliente/mis-citas');
        
        // =========================================================
        // MASCOTAS
        // =========================================================
        crearEnlace('enlaceMascotas', '/cliente/mascotas');
        crearEnlace('enlaceRegistrarMascota', '/cliente/mascotas/create');
        
        // =========================================================
        // CALIFICACIONES
        // =========================================================
        crearEnlace('enlaceCalificar', '/cliente/mis-citas');
        
        // =========================================================
        // TIENDA
        // =========================================================
        crearEnlace('enlaceTienda', '/cliente/tienda');
        crearEnlace('enlaceCarrito', '/cliente/carrito');
        crearEnlace('enlaceMisCompras', '/cliente/mis-compras');
        
        // =========================================================
        // MI CUENTA
        // =========================================================
        crearEnlace('enlacePerfil', '/cliente/perfil');
        crearEnlace('enlaceCambiarPassword', '/cliente/perfil');
        
        // =========================================================
        // NOTIFICACIONES
        // =========================================================
        crearEnlace('enlaceNotificaciones', '/mis-notificaciones');
        
        // Actualizar contador del carrito
        actualizarCarritoCount();
        
        // Escuchar cambios en localStorage
        window.addEventListener('storage', function(e) {
            if (e.key === 'carrito') {
                actualizarCarritoCount();
            }
        });
        
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
                localStorage.removeItem('carrito');
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
                    localStorage.removeItem('carrito');
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