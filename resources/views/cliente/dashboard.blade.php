<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido - Pet Spa</title>
   
</head>
<body>

    <div class="container">
        <h1>🐾 ¡Bienvenido a Pet Spa!</h1>
        <div id="userInfo">Cargando...</div>
        <hr>
        <h2>Tus Servicios</h2>
        <ul>
            <li> Agendar nueva cita</li>
            <li> Ver mis mascotas</li>
            <li> Historial de servicios</li>
            <li> Calificar atención</li>
            <li> <a href="#" id="enlacePerfil">Actualizar mis datos</a></li>
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
    // SESIÓN POR INACTIVIDAD (30 segundos de prueba)
    // =========================================================
    let tiempoInactividad;
    const TIEMPO_LIMITE = 30 * 1000; // 30 segundos (cambia a 30*60*1000 para 30 minutos)
    
    function cerrarSesionPorInactividad() {
        fetch('/api/logout', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        }).finally(() => {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/';
        });
    }
    
    function reiniciarTiempo() {
        clearTimeout(tiempoInactividad);
        tiempoInactividad = setTimeout(cerrarSesionPorInactividad, TIEMPO_LIMITE);
    }
    
    // Detectar actividad del usuario
    window.onload = reiniciarTiempo;
    document.onmousemove = reiniciarTiempo;
    document.onkeydown = reiniciarTiempo;
    document.onclick = reiniciarTiempo;
    document.onscroll = reiniciarTiempo;
    </script>
</body>
</html>