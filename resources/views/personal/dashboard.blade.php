<!DOCTYPE html>
<html>
<head>
    <title>Personal - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #e8f5e9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #2196F3; }
        button { background: red; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
        ul { list-style: none; padding: 0; }
        li { padding: 8px 0; }
        
        /* Modal */
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
            background-color: white;
            margin: 10% auto;
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 450px;
            position: relative;
            animation: modalopen 0.3s;
        }
        @keyframes modalopen {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }
        .close:hover { color: black; }
        .modal input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        .modal button { background: #4CAF50; width: 100%; margin-top: 10px; }
        .btn-cambiar {
            background: #FF9800;
            margin-top: 15px;
            width: 100%;
        }
        .strength-meter { height: 5px; background: #ddd; border-radius: 3px; margin: 8px 0; }
        .strength-meter-fill { height: 100%; width: 0%; border-radius: 3px; transition: all 0.3s; }
        .weak { background: #e74c3c; }
        .medium { background: #f39c12; }
        .strong { background: #27ae60; }
        .requirement { font-size: 12px; margin: 3px 0; }
        .requirement.valid { color: #27ae60; }
        .requirement.invalid { color: #e74c3c; }
        .mensaje { margin-top: 10px; padding: 10px; border-radius: 5px; font-size: 14px; }
        .mensaje.exito { background: #d4edda; color: #155724; }
        .mensaje.error { background: #f8d7da; color: #721c24; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>✂️ Panel de Personal</h1>
        <div class="info" id="userInfo">Cargando...</div>
        <hr>
        <h2>📅 Tu Agenda Hoy</h2>
        <ul>
            <li>📋 Ver agenda del día</li>
            <li>🐕 Mascotas en espera</li>
            <li>✅ Registrar servicio completado</li>
        </ul>
        
        <!-- Botón para abrir modal -->
        <button class="btn-cambiar" onclick="abrirModal()">🔒 Cambiar Contraseña</button>
        
        <button onclick="logout()" style="margin-top: 20px;">🚪 Cerrar Sesión</button>
    </div>

    <!-- Modal para cambiar contraseña -->
    <div id="modalPassword" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h3>🔒 Cambiar Contraseña</h3>
            <form id="cambiarPasswordForm">
                <input type="password" id="contrasena_actual" placeholder="Contraseña actual *" required>
                
                <input type="password" id="contrasena_nueva" placeholder="Nueva contraseña *" required>
                <div class="strength-meter">
                    <div class="strength-meter-fill" id="strengthFill"></div>
                </div>
                <div id="strengthText"></div>
                
                <div id="requisitos">
                    <div class="requirement invalid" id="req-length">❌ Mínimo 8 caracteres</div>
                    <div class="requirement invalid" id="req-upper">❌ Al menos una mayúscula</div>
                    <div class="requirement invalid" id="req-lower">❌ Al menos una minúscula</div>
                    <div class="requirement invalid" id="req-number">❌ Al menos un número</div>
                    <div class="requirement invalid" id="req-symbol">❌ Al menos un símbolo (@$!%*#?&)</div>
                </div>
                
                <input type="password" id="contrasena_nueva_confirmation" placeholder="Confirmar nueva contraseña *" required>
                <button type="submit">✅ Cambiar Contraseña</button>
            </form>
            <div id="passwordResultado"></div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/';

        // Cargar datos del usuario
        fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token }
        }).then(res => res.json()).then(data => {
            if (data.user) {
                document.getElementById('userInfo').innerHTML = `
                    <strong>Bienvenido/a:</strong> ${data.user.nombre_completo}<br>
                    <strong>📧 Email:</strong> ${data.user.correo}<br>
                    <strong>👔 Rol:</strong> ${data.user.rol.nombre}
                `;
            }
        }).catch(() => window.location.href = '/');

        // =========================================================
        // MODAL
        // =========================================================
        function abrirModal() {
            document.getElementById('modalPassword').style.display = 'block';
            // Limpiar formulario
            $('#cambiarPasswordForm')[0].reset();
            $('#passwordResultado').html('');
            $('#strengthFill').css('width', '0%');
            $('#strengthText').html('');
            $('.requirement').each(function() {
                $(this).removeClass('valid').addClass('invalid');
                $(this).html($(this).html().replace('✅', '❌'));
            });
        }
        
        function cerrarModal() {
            document.getElementById('modalPassword').style.display = 'none';
        }
        
        // Cerrar modal si se hace clic fuera del contenido
        window.onclick = function(event) {
            const modal = document.getElementById('modalPassword');
            if (event.target == modal) {
                cerrarModal();
            }
        }

        // =========================================================
        // MEDIDOR DE FUERZA DE CONTRASEÑA
        // =========================================================
        function checkPasswordStrength(password) {
            const checks = {
                length: password.length >= 8,
                upper: /[A-Z]/.test(password),
                lower: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                symbol: /[@$!%*#?&]/.test(password)
            };
            
            document.getElementById('req-length').innerHTML = (checks.length ? '✅' : '❌') + ' Mínimo 8 caracteres';
            document.getElementById('req-length').className = 'requirement ' + (checks.length ? 'valid' : 'invalid');
            document.getElementById('req-upper').innerHTML = (checks.upper ? '✅' : '❌') + ' Al menos una mayúscula';
            document.getElementById('req-upper').className = 'requirement ' + (checks.upper ? 'valid' : 'invalid');
            document.getElementById('req-lower').innerHTML = (checks.lower ? '✅' : '❌') + ' Al menos una minúscula';
            document.getElementById('req-lower').className = 'requirement ' + (checks.lower ? 'valid' : 'invalid');
            document.getElementById('req-number').innerHTML = (checks.number ? '✅' : '❌') + ' Al menos un número';
            document.getElementById('req-number').className = 'requirement ' + (checks.number ? 'valid' : 'invalid');
            document.getElementById('req-symbol').innerHTML = (checks.symbol ? '✅' : '❌') + ' Al menos un símbolo (@$!%*#?&)';
            document.getElementById('req-symbol').className = 'requirement ' + (checks.symbol ? 'valid' : 'invalid');
            
            let strength = 0;
            if (checks.length) strength++;
            if (checks.upper && checks.lower) strength++;
            if (checks.number) strength++;
            if (checks.symbol) strength++;
            
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            if (strength <= 2) {
                strengthFill.className = 'strength-meter-fill weak';
                strengthText.innerHTML = '🔴 Contraseña débil';
                strengthText.style.color = '#e74c3c';
            } else if (strength === 3) {
                strengthFill.className = 'strength-meter-fill medium';
                strengthText.innerHTML = '🟡 Contraseña media';
                strengthText.style.color = '#f39c12';
            } else {
                strengthFill.className = 'strength-meter-fill strong';
                strengthText.innerHTML = '🟢 Contraseña fuerte';
                strengthText.style.color = '#27ae60';
            }
            
            return checks.length && checks.upper && checks.lower && checks.number && checks.symbol;
        }
        
        document.getElementById('contrasena_nueva').addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
        
        // =========================================================
        // CAMBIAR CONTRASEÑA
        // =========================================================
        $('#cambiarPasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            const nuevaPassword = $('#contrasena_nueva').val();
            const confirmacion = $('#contrasena_nueva_confirmation').val();
            
            if (!checkPasswordStrength(nuevaPassword)) {
                $('#passwordResultado').html('<div class="mensaje error">❌ La contraseña no cumple con los requisitos de seguridad.</div>');
                return;
            }
            
            if (nuevaPassword !== confirmacion) {
                $('#passwordResultado').html('<div class="mensaje error">❌ Las contraseñas no coinciden.</div>');
                return;
            }
            
            $.ajax({
                url: '/cambiar-contrasena',
                method: 'POST',
                headers: { 
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({
                    contrasena_actual: $('#contrasena_actual').val(),
                    contrasena_nueva: nuevaPassword,
                    contrasena_nueva_confirmation: confirmacion
                }),
                success: function(response) {
                    $('#passwordResultado').html('<div class="mensaje exito">✅ ' + response.message + '</div>');
                    setTimeout(function() {
                        cerrarModal();
                        // Opcional: cerrar sesión después de cambiar contraseña
                        // logout();
                    }, 2000);
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Error al cambiar contraseña';
                    $('#passwordResultado').html('<div class="mensaje error">❌ ' + msg + '</div>');
                }
            });
        });

        // =========================================================
        // CERRAR SESIÓN
        // =========================================================
        function logout() {
            fetch('/api/logout', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token }
            }).then(() => {
                localStorage.removeItem('token');
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