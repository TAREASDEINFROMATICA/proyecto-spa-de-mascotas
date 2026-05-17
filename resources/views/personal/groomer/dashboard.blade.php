<!DOCTYPE html>
<html>
<head>
    <title>Groomer - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #e8f5e9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #4CAF50; }
        h2 { color: #2196F3; margin-top: 20px; }
        button { background: red; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .info { background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        ul { list-style: none; padding: 0; }
        li { margin: 15px 0; }
        a { text-decoration: none; background: #2196F3; color: white; padding: 10px 15px; border-radius: 5px; display: inline-block; width: 220px; text-align: center; }
        a:hover { background: #0b7dda; }
        .btn-cambiar { background: #FF9800; margin-top: 15px; width: 100%; padding: 10px; border: none; border-radius: 5px; cursor: pointer; color: white; }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background: white;
            width: 450px;
            margin: 100px auto;
            padding: 25px;
            border-radius: 10px;
            position: relative;
        }
        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            cursor: pointer;
            color: #aaa;
        }
        .close:hover { color: black; }
        .password-container {
            position: relative;
            margin: 8px 0;
        }
        .password-container input {
            width: 100%;
            padding: 8px;
            padding-right: 35px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 16px;
        }
        .strength-meter {
            height: 5px;
            background: #ddd;
            border-radius: 3px;
            margin: 8px 0;
        }
        .strength-meter-fill {
            height: 100%;
            width: 0%;
            border-radius: 3px;
            transition: all 0.3s;
        }
        .weak { background: #e74c3c; }
        .medium { background: #f39c12; }
        .strong { background: #27ae60; }
        .requirement {
            font-size: 12px;
            margin: 3px 0;
        }
        .requirement.valid { color: #27ae60; }
        .requirement.invalid { color: #e74c3c; }
        .mensaje { margin-top: 10px; padding: 10px; border-radius: 5px; font-size: 14px; }
        .mensaje.exito { background: #d4edda; color: #155724; }
        .mensaje.error { background: #f8d7da; color: #721c24; }
        hr { margin: 20px 0; }
        .btn-reporte { background: #9C27B0; width: 220px; }
        .btn-reporte:hover { background: #7B1FA2; }
        .btn-exportar { background: #f44336; width: 220px; }
        .btn-exportar:hover { background: #d32f2f; }
        .btn-calificacion { background: #ff9800; width: 220px; }
        .btn-calificacion:hover { background: #f57c00; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    @php $token = request()->query('token'); @endphp

    <div class="container">
        <h1>✂️ Panel de Groomer</h1>
        <div class="info" id="userInfo">Cargando...</div>
        <hr>
        
        <h2>📋 Menú Principal</h2>
        <ul>
            <li><a href="#" id="enlaceMisCitas">📅 Mis Citas</a></li>
            <li><a href="#" id="enlaceMisMascotas">🐕 Mascotas Asignadas</a></li>
            <li><a href="#" id="enlaceChecklist">✅ Checklist de Servicios</a></li>
            <li><a href="#" id="enlaceGaleria">📸 Galería de Fotos</a></li>
            <li><a href="#" id="enlaceInsumos">📦 Mis Insumos</a></li>
        </ul>
        
        <h2>📊 Reportes y Estadísticas</h2>
        <ul>
            <li><a href="#" id="enlaceCalificaciones" class="btn-calificacion">⭐ Mis Calificaciones</a></li>
            <li><a href="#" id="enlaceEstadisticas" class="btn-reporte">📈 Mis Estadísticas</a></li>
            <a href="#" id="enlaceExportarCSV" class="btn-exportar">📄 Exportar Servicios (CSV)</a>
        </ul>
        
        <button class="btn-cambiar" onclick="abrirModal()">🔒 Cambiar Contraseña</button>
        <button onclick="logout()" style="margin-top: 20px;">🚪 Cerrar Sesión</button>
    </div>

    <!-- Modal para cambiar contraseña -->
    <div id="modalPassword" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h3>🔒 Cambiar Contraseña</h3>
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
                <div id="strengthText"></div>
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
                <button type="submit">✅ Cambiar Contraseña</button>
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
                console.log(`Enlace creado: ${id} -> ${enlace.href}`);
            } else {
                console.warn(`Elemento no encontrado: ${id}`);
            }
        }

        // =========================================================
        // MENÚ PRINCIPAL
        // =========================================================
        crearEnlace('enlaceMisCitas', '/groomer/mis-citas');
        crearEnlace('enlaceMisMascotas', '/groomer/mis-mascotas');
        crearEnlace('enlaceChecklist', '/groomer/checklist');
        crearEnlace('enlaceGaleria', '/groomer/galeria');
        crearEnlace('enlaceInsumos', '/groomer/insumos');
        
        // =========================================================
        // REPORTES Y ESTADÍSTICAS (NUEVOS)
        // =========================================================
        crearEnlace('enlaceCalificaciones', '/groomer/mis-calificaciones');
        crearEnlace('enlaceEstadisticas', '/groomer/mis-estadisticas');
        crearEnlace('enlaceExportarCSV', '/groomer/exportar-csv');

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

        function abrirModal() { document.getElementById('modalPassword').style.display = 'block'; }
        function cerrarModal() { 
            document.getElementById('modalPassword').style.display = 'none';
            $('#cambiarPasswordForm')[0].reset();
            $('#passwordResultado').html('');
            $('#strengthFill').css('width', '0%');
            $('#strengthText').html('');
            $('.requirement').each(function() {
                $(this).removeClass('valid').addClass('invalid');
                $(this).html($(this).html().replace('✅', '❌'));
            });
        }

        fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token }
        }).then(res => res.json()).then(data => {
            if (data.user) {
                document.getElementById('userInfo').innerHTML = `
                    <strong>Bienvenido/a:</strong> ${data.user.nombres} ${data.user.apellidos}<br>
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
            const nueva = $('#contrasena_nueva').val();
            const conf = $('#contrasena_nueva_confirmation').val();
            if (!checkPasswordStrength(nueva)) {
                $('#passwordResultado').html('<div class="mensaje error">❌ Contraseña no cumple requisitos</div>');
                return;
            }
            if (nueva !== conf) {
                $('#passwordResultado').html('<div class="mensaje error">❌ Las contraseñas no coinciden</div>');
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
                    $('#passwordResultado').html('<div class="mensaje exito">✅ ' + res.message + '</div>');
                    setTimeout(cerrarModal, 2000);
                },
                error: function(xhr) {
                    $('#passwordResultado').html('<div class="mensaje error">❌ ' + (xhr.responseJSON?.message || 'Error') + '</div>');
                }
            });
        });

        let tiempoInactividad;
        const TIEMPO_LIMITE = 30 * 1000;
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
    </script>
</body>
</html>