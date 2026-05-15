<!DOCTYPE html>
<html>
<head>
    <title>Recepción - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #e3f2fd; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #2196F3; }
        button { background: red; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
        a { text-decoration: none; color: #2196F3; }
        a:hover { text-decoration: underline; }
        .btn-cambiar { background: #FF9800; margin-top: 15px; width: 100%; padding: 10px; border: none; border-radius: 5px; cursor: pointer; color: white; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    @php $token = request()->query('token'); @endphp

    <div class="container">
        <h1>📞 Panel de Recepción</h1>
        <div class="info" id="userInfo">Cargando...</div>
        <hr>
        <h2>📋 Gestión</h2>
        <ul>
            <li>📊 Ver estadísticas</li>
            <li>📅 <a href="#" id="enlaceAgenda">Agenda Maestra</a></li>
            <li>⏳ <a href="#" id="enlaceCitasPendientes">Citas Pendientes</a></li>
            <li>➕ <a href="#" id="enlaceCrearCita">Crear Cita</a></li>
            <li>👥 <a href="#" id="enlaceClientes">Gestionar Clientes</a></li>
            <li>🐕 <a href="#" id="enlaceMascotas">Todas las Mascotas</a></li>
        </ul>
        
        <button class="btn-cambiar" onclick="abrirModal()">🔒 Cambiar Contraseña</button>
        <button onclick="logout()" style="margin-top: 20px;">🚪 Cerrar Sesión</button>
    </div>

    <!-- Modal para cambiar contraseña -->
    <div id="modalPassword" class="modal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:white; width:400px; margin:100px auto; padding:20px; border-radius:10px;">
            <span onclick="cerrarModal()" style="float:right; cursor:pointer;">&times;</span>
            <h3>🔒 Cambiar Contraseña</h3>
            <form id="cambiarPasswordForm">
                <input type="password" id="contrasena_actual" placeholder="Contraseña actual" style="width:100%; padding:8px; margin:8px 0;">
                <input type="password" id="contrasena_nueva" placeholder="Nueva contraseña" style="width:100%; padding:8px; margin:8px 0;">
                <input type="password" id="contrasena_nueva_confirmation" placeholder="Confirmar contraseña" style="width:100%; padding:8px; margin:8px 0;">
                <button type="submit" style="background:#4CAF50; color:white; padding:10px; width:100%; border:none; border-radius:5px;">Cambiar</button>
            </form>
            <div id="passwordResultado"></div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/';

        // Crear enlaces con token
        const enlaceAgenda = document.getElementById('enlaceAgenda');
        if (enlaceAgenda) {
            enlaceAgenda.href = '/admin/agenda?token=' + token;
        }
        
        const enlaceCitasPendientes = document.getElementById('enlaceCitasPendientes');
        if (enlaceCitasPendientes) {
            enlaceCitasPendientes.href = '/personal/citas-pendientes?token=' + token;
        }
        
        const enlaceCrearCita = document.getElementById('enlaceCrearCita');
        if (enlaceCrearCita) {
            enlaceCrearCita.href = '/admin/citas/create?token=' + token;
        }
        
        const enlaceClientes = document.getElementById('enlaceClientes');
        if (enlaceClientes) {
            enlaceClientes.href = '/admin/clientes?token=' + token;
        }
        
        const enlaceMascotas = document.getElementById('enlaceMascotas');
        if (enlaceMascotas) {
            enlaceMascotas.href = '/admin/mascotas?token=' + token;
        }

        function abrirModal() { document.getElementById('modalPassword').style.display = 'block'; }
        function cerrarModal() { document.getElementById('modalPassword').style.display = 'none'; }

        fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token }
        }).then(res => res.json()).then(data => {
            if (data.user) {
                document.getElementById('userInfo').innerHTML = `
                    <strong>Bienvenido/a:</strong> ${data.user.nombre_completo}<br>
                    <strong>Email:</strong> ${data.user.correo}<br>
                    <strong>Rol:</strong> ${data.user.rol.nombre}
                `;
            }
        });

        function logout() {
            fetch('/api/logout', { method: 'POST', headers: { 'Authorization': 'Bearer ' + token } })
                .then(() => { localStorage.removeItem('token'); window.location.href = '/'; });
        }

        $('#cambiarPasswordForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '/cambiar-contrasena',
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                data: JSON.stringify({
                    contrasena_actual: $('#contrasena_actual').val(),
                    contrasena_nueva: $('#contrasena_nueva').val(),
                    contrasena_nueva_confirmation: $('#contrasena_nueva_confirmation').val()
                }),
                success: function(response) {
                    $('#passwordResultado').html('<div style="color:green;">✅ ' + response.message + '</div>');
                    setTimeout(cerrarModal, 2000);
                },
                error: function(xhr) {
                    $('#passwordResultado').html('<div style="color:red;">❌ ' + (xhr.responseJSON?.message || 'Error') + '</div>');
                }
            });
        });

        // SESIÓN POR INACTIVIDAD
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