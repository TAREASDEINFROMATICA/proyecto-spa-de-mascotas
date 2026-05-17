<!DOCTYPE html>
<html>
<head>
    <title>Admin - Pet Spa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #4CAF50; margin-bottom: 20px; }
        h2 { color: #2196F3; margin: 20px 0 10px 0; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        ul { list-style: none; }
        li { margin: 10px 0; padding: 8px; background: #f5f5f5; border-radius: 5px; }
        li:hover { background: #e0e0e0; }
        a { text-decoration: none; color: #333; display: block; }
        a:hover { color: #4CAF50; }
        button { background: #f44336; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; font-size: 16px; }
        button:hover { background: #d32f2f; }
        .submenu { margin-left: 20px; margin-top: 5px; }
        .submenu li { background: #fff; border-left: 3px solid #4CAF50; }
        hr { margin: 15px 0; border-color: #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐾 Panel de Administrador</h1>
        <div class="info" id="userInfo">Cargando datos...</div>
        <hr>
        
        <h2>📊 Gestión Principal</h2>
        <ul>
            <li><a href="#" id="enlaceEstadisticas">📈 Ver estadísticas</a></li>
            <li><a href="#" id="enlaceEmpleados">👥 Gestionar Empleados</a></li>
            <li><a href="#" id="enlaceServicios">✂️ Gestionar Servicios</a></li>
            <li><a href="#" id="enlaceAgenda">📅 Agenda Maestra</a></li>
            <li><a href="#" id="enlaceCitas">📋 Ver todas las citas</a></li>
        </ul>

        <h2>💰 Ventas y Finanzas</h2>
        <ul>
            <li><a href="#" id="enlaceReportes">📊 Reportes financieros</a></li>
            <li><a href="#" id="enlaceVentas">🛒 Historial de Ventas</a></li>
        </ul>

        <h2>📦 Inventario</h2>
        <ul>
            <li><a href="#" id="enlaceInsumos">🧴 Gestión de Insumos (Grooming)</a></li>
            <li><a href="#" id="enlaceProductos">🏪 Gestión de Productos (Tienda)</a></li>
            <li><a href="#" id="enlaceAlertasStock">⚠️ Alertas de Stock Bajo</a></li>
        </ul>

        <h2>👤 Gestión de Usuarios</h2>
        <ul>
            <li><a href="#" id="enlaceClientes">👥 Gestionar Clientes</a></li>
            <li><a href="#" id="enlaceMascotas">🐕 Todas las Mascotas</a></li>
        </ul>

        <h2>🔐 Seguridad</h2>
        <ul>
            <li><a href="#" id="enlace2fa">🔑 Configurar 2FA (Google Authenticator)</a></li>
            <li><a href="#" id="enlaceLogs">📜 Ver Logs de Auditoría</a></li>
        </ul>

        <button onclick="logout()">🚪 Cerrar Sesión</button>
    </div>

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
                console.log(`Enlace creado: ${id} -> ${enlace.href}`);
            } else {
                console.warn(`Elemento no encontrado: ${id}`);
            }
        }
        
        // =========================================================
        // GESTIÓN PRINCIPAL
        // =========================================================
        crearEnlace('enlaceEstadisticas', '/admin/dashboard');  // Temporal, misma página
        crearEnlace('enlaceEmpleados', '/admin/empleados');
        crearEnlace('enlaceServicios', '/admin/servicios');
        crearEnlace('enlaceAgenda', '/admin/agenda');
        crearEnlace('enlaceCitas', '/admin/citas');
        
        // =========================================================
        // VENTAS Y FINANZAS
        // =========================================================
        crearEnlace('enlaceReportes', '/admin/reportes');
        crearEnlace('enlaceVentas', '/admin/ventas');
        
        // =========================================================
        // INVENTARIO
        // =========================================================
        crearEnlace('enlaceInsumos', '/admin/insumos');
        crearEnlace('enlaceProductos', '/admin/productos');
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
        
        // Cargar datos del usuario
        fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(res => res.json())
        .then(data => {
            if (data.user) {
                document.getElementById('userInfo').innerHTML = `
                    <strong>👋 Bienvenido:</strong> ${data.user.nombres} ${data.user.apellidos}<br>
                    <strong>📧 Email:</strong> ${data.user.correo}<br>
                    <strong>🎭 Rol:</strong> ${data.user.rol.nombre}
                `;
            } else {
                document.getElementById('userInfo').innerHTML = '<span style="color: red;">❌ Error al cargar datos</span>';
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
    </script>

    <!-- ========================================================= -->
    <!-- SESIÓN POR INACTIVIDAD -->
    <!-- ========================================================= -->
    <script>
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
    </script>
</body>
</html>