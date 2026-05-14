<!DOCTYPE html>
<html>
<head>
    <title>Admin - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #1a1a2e; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #4CAF50; }
        button { background: red; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .info { background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
        a { text-decoration: none; color: #2196F3; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐾 Panel de Administrador</h1>
        <div class="info" id="userInfo">Cargando datos...</div>
        <hr>
        <h2>Menú Principal</h2>
        <ul>
            <li>📊 Ver estadísticas</li>
            <li>👥 <a href="#" id="enlaceEmpleados">Gestionar Empleados</a></li>
            <li>🐕 <a href="#" id="enlaceMascotas">Todas las Mascotas</a></li>
            <li>📅 Ver todas las citas</li>
            <li>💰 Reportes financieros</li>
            <li>🔐 <a href="#" id="enlace2fa">Configurar 2FA (Google Authenticator)</a></li>
            <li>📋 <a href="#" id="enlaceLogs">Ver Logs de Auditoría</a></li>
            <li>👥 <a href="#" id="enlaceClientes">Gestionar Clientes</a></li>
        </ul>
        <button onclick="logout()">Cerrar Sesión</button>
    </div>

    <script>
        const token = localStorage.getItem('token');
        
        if (!token) {
            window.location.href = '/';
        }
        
        // Mostrar token en consola para depuración
        console.log('Token encontrado:', token);
        
        // Crear enlaces con token
        const enlace2fa = document.getElementById('enlace2fa');
        if (enlace2fa) {
            enlace2fa.href = '/admin/configurar-2fa?token=' + token;
            console.log('Enlace 2FA:', enlace2fa.href);
        }
        
        const enlaceEmpleados = document.getElementById('enlaceEmpleados');
        if (enlaceEmpleados) {
            enlaceEmpleados.href = '/admin/empleados?token=' + token;
            console.log('Enlace Empleados:', enlaceEmpleados.href);
        }
        
        const enlaceLogs = document.getElementById('enlaceLogs');
        if (enlaceLogs) {
            enlaceLogs.href = '/admin/logs?token=' + token;
        }
        
        const enlaceClientes = document.getElementById('enlaceClientes');
        if (enlaceClientes) {
            enlaceClientes.href = '/admin/clientes?token=' + token;
        }
        
        const enlaceMascotas = document.getElementById('enlaceMascotas');
        if (enlaceMascotas) {
            enlaceMascotas.href = '/admin/mascotas?token=' + token;
            console.log('Enlace Mascotas:', enlaceMascotas.href);
        }
        
        // Cargar datos del usuario
        fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token }
        }).then(res => res.json()).then(data => {
            if (data.user) {
                document.getElementById('userInfo').innerHTML = `
                    <strong>Bienvenido:</strong> ${data.user.nombre_completo}<br>
                    <strong>Email:</strong> ${data.user.correo}<br>
                    <strong>Rol:</strong> ${data.user.rol.nombre}
                `;
            }
        }).catch(() => {
            window.location.href = '/';
        });

        function logout() {
            fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = '/';
            });
        }
        
        // =========================================================
        // SESIÓN POR INACTIVIDAD
        // =========================================================
        let tiempoInactividad;
        const TIEMPO_LIMITE = 30 * 1000;
        
        function cerrarSesionPorInactividad() {
            fetch('/api/logout', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token }
            }).finally(() => {
                localStorage.removeItem('token');
                window.location.href = '/';
            });
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