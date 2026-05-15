<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #fff3e0; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #FF9800; }
        button { background: red; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .info { background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
        a { text-decoration: none; color: #2196F3; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    @php
        $token = request()->query('token');
    @endphp

    <div class="container">
        <h1>🐾 ¡Bienvenido a Pet Spa!</h1>
        <div class="info" id="userInfo">Cargando datos...</div>
        <hr>
        <h2>Tus Servicios</h2>
        <ul>
            <li>📅 Agendar nueva cita</li>
            <li>🐕 <a href="#" id="enlaceMascotas">Ver mis mascotas</a></li>
    
            <li>📋 Historial de servicios</li>
            <li>⭐ Calificar atención</li>
            <li>👤 <a href="#" id="enlacePerfil">Actualizar mis datos</a></li>
       <li>📅 <a href="#" id="enlaceSolicitarCita">Solicitar Cita</a></li>
<li>📋 <a href="#" id="enlaceMisCitas">Mis Citas</a></li>
</ul>
        <button onclick="logout()">Cerrar Sesión</button>
    </div>

    <script>
        const token = localStorage.getItem('token');
        
        if (!token) {
            window.location.href = '/';
        }
        
        // Crear enlace con token
        const enlacePerfil = document.getElementById('enlacePerfil');
        if (enlacePerfil) {
            enlacePerfil.href = '/cliente/perfil?token=' + token;
        }
        const enlaceMascotas = document.getElementById('enlaceMascotas');
        if (enlaceMascotas) {
            enlaceMascotas.href = '/cliente/mascotas?token=' + token;
        }
        
        const enlaceSolicitarCita = document.getElementById('enlaceSolicitarCita');
        if (enlaceSolicitarCita) {
            enlaceSolicitarCita.href = '/cliente/solicitar-cita?token=' + token;
        }
        
        const enlaceMisCitas = document.getElementById('enlaceMisCitas');
        if (enlaceMisCitas) {
            enlaceMisCitas.href = '/cliente/mis-citas?token=' + token;
        }

        // Cargar datos del usuario
        fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token }
        }).then(res => res.json()).then(data => {
            if (data.user) {
                document.getElementById('userInfo').innerHTML = `
                    <strong>Hola:</strong> ${data.user.nombre_completo}<br>
                    <strong>Email:</strong> ${data.user.correo}
                `;
            }
        }).catch(() => {
            window.location.href = '/';
        });

        function logout() {
            fetch('/api/logout', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token }
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