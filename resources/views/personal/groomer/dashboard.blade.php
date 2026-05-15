<!DOCTYPE html>
<html>
<head>
    <title>Groomer - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #e8f5e9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #4CAF50; }
        button { background: red; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .info { background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
        a { text-decoration: none; color: #4CAF50; }
        a:hover { text-decoration: underline; }
        .btn-cambiar { background: #FF9800; margin-top: 15px; width: 100%; padding: 10px; border: none; border-radius: 5px; cursor: pointer; color: white; }
        
        /* Estilos para el modal */
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
        
        /* Medidor de fuerza */
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
        .weak { background: #e74c3c; width: 33%; }
        .medium { background: #f39c12; width: 66%; }
        .strong { background: #27ae60; width: 100%; }
        
        /* Requisitos */
        .requirement {
            font-size: 12px;
            margin: 3px 0;
        }
        .requirement.valid { color: #27ae60; }
        .requirement.invalid { color: #e74c3c; }
        
        /* Campo con ojo */
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
        
        .mensaje { margin-top: 10px; padding: 10px; border-radius: 5px; font-size: 14px; }
        .mensaje.exito { background: #d4edda; color: #155724; }
        .mensaje.error { background: #f8d7da; color: #721c24; }
        
        input, select { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    @php $token = request()->query('token'); @endphp

    <div class="container">
        <h1>✂️ Panel de Groomer</h1>
        <div class="info" id="userInfo">Cargando...</div>
        <hr>
        <h2>📅 Mi Agenda</h2>
        <ul>
            <li>📋 Mi Agenda de Hoy (en construcción)</li>
            <li>🐕 Mascotas Asignadas (en construcción)</li>
            <li>✅ Checklist de Servicios (en construcción)</li>
            <li>📸 Galería de Fotos (en construcción)</li>
            <li>📦 Mis Insumos (en construcción)</li>
        </ul>
        
        <button class="btn-cambiar" onclick="abrirModal()">🔒 Cambiar Contraseña</button>
        <button onclick="logout()" style="margin-top: 20px;">🚪 Cerrar Sesión</button>
    </div>

    <!-- Modal para cambiar contraseña MEJORADO -->
    <div id="modalPassword" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h3>🔒 Cambiar Contraseña</h3>
            
            <form id="cambiarPasswordForm">
                <!-- Contraseña actual -->
                <label>Contraseña actual *</label>
                <div class="password-container">
                    <input type="password" id="contrasena_actual" placeholder="Ingrese su contraseña actual" required>
                    <span class="toggle-password" onclick="togglePassword('contrasena_actual')">👁️</span>
                </div>
                
                <!-- Nueva contraseña -->
                <label>Nueva contraseña *</label>
                <div class="password-container">
                    <input type="password" id="contrasena_nueva" placeholder="Mínimo 8 caracteres" required>
                    <span class="toggle-password" onclick="togglePassword('contrasena_nueva')">👁️</span>
                </div>
                
                <!-- Medidor de fuerza -->
                <div class="strength-meter">
                    <div class="strength-meter-fill" id="strengthFill"></div>
                </div>
                <div id="strengthText" style="font-size: 12px;"></div>
                
                <!-- Requisitos -->
                <div id="requisitos">
                    <div class="requirement invalid" id="req-length">❌ Mínimo 8 caracteres</div>
                    <div class="requirement invalid" id="req-upper">❌ Al menos una mayúscula</div>
                    <div class="requirement invalid" id="req-lower">❌ Al menos una minúscula</div>
                    <div class="requirement invalid" id="req-number">❌ Al menos un número</div>
                    <div class="requirement invalid" id="req-symbol">❌ Al menos un símbolo (@$!%*#?&)</div>
                </div>
                
                <!-- Confirmar contraseña -->
                <label>Confirmar nueva contraseña *</label>
                <div class="password-container">
                    <input type="password" id="contrasena_nueva_confirmation" placeholder="Repita la nueva contraseña" required>
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

        // Mostrar/ocultar contraseña
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }

        // Medidor de fuerza de contraseña
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
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                data: JSON.stringify({
                    contrasena_actual: $('#contrasena_actual').val(),
                    contrasena_nueva: nuevaPassword,
                    contrasena_nueva_confirmation: confirmacion
                }),
                success: function(response) {
                    $('#passwordResultado').html('<div class="mensaje exito">✅ ' + response.message + '</div>');
                    setTimeout(cerrarModal, 2000);
                },
                error: function(xhr) {
                    $('#passwordResultado').html('<div class="mensaje error">❌ ' + (xhr.responseJSON?.message || 'Error') + '</div>');
                }
            });
        });
          
        // =========================================================
        // SESIÓN POR INACTIVIDAD (30 segundos)
        // =========================================================
        let tiempoInactividad;
        const TIEMPO_LIMITE = 30 * 1000; // 30 segundos
        
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
        
        window.onload = reiniciarTiempo;
        document.onmousemove = reiniciarTiempo;
        document.onkeydown = reiniciarTiempo;
        document.onclick = reiniciarTiempo;
        document.onscroll = reiniciarTiempo;
    </script>
</body>
</html>