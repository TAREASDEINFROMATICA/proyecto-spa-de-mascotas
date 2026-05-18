<!DOCTYPE html>
<html>
<head>
    <title>Recepción - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; display: flex; align-items: center; justify-content: center; gap: 12px; }
        .header p { opacity: 0.9; margin-top: 8px; font-size: 14px; }
        .content { padding: 30px; }
        
        .info-card { background: linear-gradient(135deg, #e3f2fd 0%, #bbdef5 100%); padding: 20px; border-radius: 16px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .info-card i { font-size: 48px; color: #1976D2; }
        .info-text { flex: 1; }
        .info-text strong { font-size: 18px; color: #1565C0; }
        .info-text p { color: #555; margin-top: 5px; }
        
        .notif-badge { background: #f44336; color: white; border-radius: 50%; padding: 2px 8px; font-size: 11px; font-weight: 600; margin-left: 8px; }
        
        h2 { font-size: 18px; color: #333; margin: 25px 0 15px 0; display: flex; align-items: center; gap: 10px; padding-bottom: 8px; border-bottom: 2px solid #e0e0e0; }
        h2 i { color: #2196F3; font-size: 20px; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; margin-bottom: 10px; }
        .menu-item { background: #f8f9fa; border-radius: 12px; transition: all 0.3s ease; overflow: hidden; }
        .menu-item:hover { background: #e8f5e9; transform: translateX(5px); }
        .menu-item a { display: flex; align-items: center; gap: 12px; padding: 14px 18px; text-decoration: none; color: #333; font-weight: 500; transition: all 0.3s; }
        .menu-item a i { width: 28px; font-size: 18px; color: #2196F3; }
        .menu-item a:hover { color: #2196F3; }
        
        .btn-logout { background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white; border: none; padding: 14px 24px; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 20px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-logout:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(244,67,54,0.3); }
        .btn-cambiar { background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; border: none; padding: 12px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 20px; width: 100%; }
        .btn-cambiar:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,152,0,0.3); }
        
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 20px; width: 450px; max-width: 90%; padding: 25px; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .modal-content h3 { margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .modal-content input { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 12px; margin: 10px 0; font-family: 'Inter', sans-serif; }
        .modal-content input:focus { outline: none; border-color: #2196F3; }
        .modal-buttons { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .btn-confirm { background: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
        .btn-cancel { background: #607d8b; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
        .close { float: right; font-size: 24px; cursor: pointer; color: #999; }
        .close:hover { color: #333; }
        .mensaje-exito { background: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-top: 10px; }
        .mensaje-error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-top: 10px; }
        
        @media (max-width: 640px) { .menu-grid { grid-template-columns: 1fr; } .info-card { flex-direction: column; text-align: center; } }
    </style>
</head>
<body>
    @php $token = request()->query('token'); @endphp

    <div class="container">
        <div class="header">
            <h1><i class="fas fa-phone-alt"></i> Panel de Recepción</h1>
            <p>Gestión de citas, clientes y agenda</p>
        </div>
        
        <div class="content">
            <div class="info-card">
                <i class="fas fa-user-circle"></i>
                <div class="info-text">
                    <strong id="userName">Cargando...</strong>
                    <p id="userEmail">Cargando datos del usuario...</p>
                    <p id="userRol" style="margin-top: 5px;"></p>
                </div>
                <div id="notificacionesBtn" style="position: relative;">
                    <a href="#" id="enlaceNotificaciones" style="background: #2196F3; color: white; padding: 10px 15px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-bell"></i> Notificaciones
                        <span id="notifCount" class="notif-badge" style="display: none;">0</span>
                    </a>
                </div>
            </div>
            
            <h2><i class="fas fa-calendar-alt"></i> Gestión de Citas</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceAgenda"><i class="fas fa-calendar-week"></i> Agenda Maestra</a></div>
                <div class="menu-item"><a href="#" id="enlaceCitasPendientes"><i class="fas fa-clock"></i> Citas Pendientes</a></div>
                <div class="menu-item"><a href="#" id="enlaceCrearCita"><i class="fas fa-plus-circle"></i> Crear Cita</a></div>
            </div>
            
            <h2><i class="fas fa-users"></i> Gestión de Clientes</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceClientes"><i class="fas fa-user-friends"></i> Gestionar Clientes</a></div>
                <div class="menu-item"><a href="#" id="enlaceMascotas"><i class="fas fa-dog"></i> Todas las Mascotas</a></div>
            </div>
            
            <button class="btn-cambiar" onclick="abrirModal()">
                <i class="fas fa-key"></i> Cambiar Contraseña
            </button>
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
                <input type="password" id="contrasena_actual" placeholder="Contraseña actual" required>
                <input type="password" id="contrasena_nueva" placeholder="Nueva contraseña" required>
                <input type="password" id="contrasena_nueva_confirmation" placeholder="Confirmar contraseña" required>
                <div class="modal-buttons">
                    <button type="submit" class="btn-confirm">✅ Cambiar</button>
                    <button type="button" onclick="cerrarModal()" class="btn-cancel">Cancelar</button>
                </div>
            </form>
            <div id="passwordResultado"></div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/';
        
        // Función para crear enlaces con token
        function crearEnlace(id, url) {
            const enlace = document.getElementById(id);
            if (enlace) {
                enlace.href = url + '?token=' + token;
            }
        }
        
        // Crear enlaces
        crearEnlace('enlaceAgenda', '/admin/agenda');
        crearEnlace('enlaceCitasPendientes', '/personal/citas-pendientes');
        crearEnlace('enlaceCrearCita', '/admin/citas/create');
        crearEnlace('enlaceClientes', '/admin/clientes');
        crearEnlace('enlaceMascotas', '/admin/mascotas');
        crearEnlace('enlaceNotificaciones', '/mis-notificaciones');
        
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
        
        // Actualizar cada 30 segundos
        setInterval(actualizarNotificaciones, 30000);
        actualizarNotificaciones();
        
        // =========================================================
        // MODAL CAMBIAR CONTRASEÑA
        // =========================================================
        function abrirModal() { document.getElementById('modalPassword').style.display = 'flex'; }
        function cerrarModal() { 
            document.getElementById('modalPassword').style.display = 'none';
            document.getElementById('passwordResultado').innerHTML = '';
            document.getElementById('cambiarPasswordForm').reset();
        }
        
        // =========================================================
        // CARGAR DATOS DEL USUARIO
        // =========================================================
        fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(res => res.json())
        .then(data => {
            if (data.user) {
                document.getElementById('userName').innerHTML = `👋 Bienvenido, ${data.user.nombres} ${data.user.apellidos}`;
                document.getElementById('userEmail').innerHTML = `📧 ${data.user.correo}`;
                document.getElementById('userRol').innerHTML = `<i class="fas fa-tag"></i> Rol: <strong>${data.user.rol.nombre}</strong>`;
            }
        })
        .catch(() => { window.location.href = '/'; });
        
        // =========================================================
        // LOGOUT
        // =========================================================
        function logout() {
            fetch('/api/logout', { method: 'POST', headers: { 'Authorization': 'Bearer ' + token } })
                .finally(() => { localStorage.removeItem('token'); window.location.href = '/'; });
        }
        
        // =========================================================
        // CAMBIAR CONTRASEÑA
        // =========================================================
        $('#cambiarPasswordForm').on('submit', function(e) {
            e.preventDefault();
            const nueva = $('#contrasena_nueva').val();
            const conf = $('#contrasena_nueva_confirmation').val();
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
                success: function(response) {
                    $('#passwordResultado').html('<div class="mensaje-exito">✅ ' + response.message + '</div>');
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
            fetch('/api/logout', { method: 'POST', headers: { 'Authorization': 'Bearer ' + token } })
                .finally(() => { localStorage.removeItem('token'); window.location.href = '/'; });
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