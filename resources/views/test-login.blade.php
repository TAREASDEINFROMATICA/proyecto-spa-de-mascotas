<!DOCTYPE html>
<html>
<head>
    <title>Login Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 450px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        
        /* Campo de contraseña con ojo */
        .password-container {
            position: relative;
            margin: 10px 0;
        }
        .password-container input {
            width: 100%;
            padding: 12px;
            padding-right: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #999;
            user-select: none;
        }
        .toggle-password:hover {
            color: #667eea;
        }
        
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover { transform: translateY(-2px); }
        
        .captcha-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            text-align: center;
        }
        .captcha-refresh {
            background: #607d8b;
            margin-top: 10px;
            padding: 8px 15px;
            font-size: 14px;
            width: auto;
            display: inline-block;
        }
        .captcha-refresh:hover { background: #455a64; }
        .flex-center { display: flex; justify-content: center; gap: 10px; align-items: center; flex-wrap: wrap; }
        
        #resultado { margin-top: 20px; }
        .error {
            color: red;
            background: #ffebee;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .success {
            color: green;
            background: #e8f5e9;
            padding: 12px;
            border-radius: 8px;
        }
        .loading { text-align: center; color: #666; }
        .blocked {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            margin-top: 10px;
        }
        #contador { font-size: 24px; font-weight: bold; text-align: center; margin-top: 10px; color: #d9534f; }
        
        /* Página de 2FA */
        .codigo-container {
            text-align: center;
            padding: 20px;
        }
        .codigo-input {
            font-size: 24px;
            letter-spacing: 10px;
            text-align: center;
        }
        
        /* Reenviar verificación */
        .reenviar-link {
            text-align: center;
            margin-top: 15px;
        }
        .reenviar-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        .reenviar-form {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .btn-cancelar {
            background: #607d8b;
            margin-top: 5px;
        }
        .btn-cancelar:hover { background: #455a64; }
        .input-small {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    
    <div class="container">
        <h2>🐾 LOGIN SPA DE MASCOTAS</h2>
        
        <!-- Formulario de login normal -->
        <div id="loginPanel">
            <form id="loginForm">
                <input type="email" id="correo" placeholder="Correo" value="admin@spamascota.com" required>
                
                <div class="password-container">
                    <input type="password" id="contrasena" placeholder="Contraseña" required>
                    <span class="toggle-password" onclick="togglePassword()">👁️</span>
                </div>
                
                <div class="captcha-container">
                    <div id="captchaCode" style="margin-bottom: 10px;">Cargando captcha...</div>
                    <div class="flex-center">
                        <button type="button" onclick="refreshCaptcha()" class="captcha-refresh">🔄 Actualizar código</button>
                    </div>
                    <input type="text" id="captcha" placeholder="Escribe el código de la imagen" style="margin-top: 10px;" required>
                </div>
                
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
        
        <hr style="margin: 20px 0;">
        
        <a href="/auth/google" style="display: block; text-align: center; background: #4285F4; color: white; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: bold;">
            🚀 Continuar con Google
        </a>
        
        <div style="text-align: center; margin-top: 15px;">
            <span style="color: #666;">¿No tienes cuenta?</span>
            <a href="/registro" style="color: #667eea; text-decoration: none;"> Regístrate aquí</a>
        </div>
        
        <!-- REENVIAR VERIFICACIÓN -->
        <div class="reenviar-link">
            <a href="#" id="reenviarLink"> 📧 ¿No recibiste el correo de verificación? Reenviar enlace</a>
        </div>
        
        <div id="reenviarForm" class="reenviar-form">
            <input type="email" id="emailReenviar" placeholder="Ingresa tu correo electrónico" class="input-small">
            <button onclick="reenviarVerificacion()" style="background: #2196F3;">📧 Reenviar enlace</button>
            <button onclick="cerrarReenviar()" class="btn-cancelar">Cancelar</button>
            <div id="resultadoReenvio"></div>
        </div>
        
        <!-- Panel de 2FA -->
        <div id="twofaPanel" style="display: none;">
            <div class="info" style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <strong>🔐 Autenticación de Dos Factores</strong><br>
                Ingresa el código de 6 dígitos de Google Authenticator
            </div>
            <input type="text" id="codigo2fa" class="codigo-input" placeholder="000000" maxlength="6" style="font-size: 24px; text-align: center; letter-spacing: 5px;">
            <button onclick="verificar2FA()">✅ Verificar y Acceder</button>
            <button onclick="volverALogin()" style="background: #607d8b; margin-top: 10px;">← Volver al login</button>
        </div>
        
        <div id="resultado"></div>
    </div>

    <script>
        let currentCaptchaCode = '';
        let tiempoRestanteIntervalo = null;
        let pendingLoginData = null;
        
        // =========================================================
        // MOSTRAR/OCULTAR CONTRASEÑA
        // =========================================================
        function togglePassword() {
            const passwordInput = document.getElementById('contrasena');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.innerHTML = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleIcon.innerHTML = '👁️';
            }
        }
        
        // Generar CAPTCHA
        let refreshing = false;

        function refreshCaptcha() {
            if (refreshing) return;
            refreshing = true;

            $('#captchaCode').html('<span style="color:#666;">⏳ Generando...</span>');
            
            $.ajax({
                url: '/captcha/generate',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        currentCaptchaCode = response.code;
                        $('#captchaCode').html(response.html);
                        $('#captcha').val('');
                    } else {
                        $('#captchaCode').html('❌ Error al generar captcha');
                    }
                },
                error: function() {
                    $('#captchaCode').html('❌ Error al generar captcha');
                },
                complete: function() {
                    refreshing = false;
                }
            });
        }
        
        // Mostrar panel de 2FA
        function mostrarPanel2FA(userId) {
            pendingLoginData = { user_id: userId };
            document.getElementById('loginPanel').style.display = 'none';
            document.getElementById('twofaPanel').style.display = 'block';
            document.getElementById('resultado').innerHTML = '';
            document.getElementById('codigo2fa').value = '';
            document.getElementById('codigo2fa').focus();
        }
        
        function volverALogin() {
            document.getElementById('loginPanel').style.display = 'block';
            document.getElementById('twofaPanel').style.display = 'none';
            document.getElementById('resultado').innerHTML = '';
            pendingLoginData = null;
            refreshCaptcha();
        }
        
        function verificar2FA() {
            const codigo = document.getElementById('codigo2fa').value;
            
            if (!codigo || codigo.length !== 6) {
                document.getElementById('resultado').innerHTML = '<div class="error">❌ Ingresa el código de 6 dígitos</div>';
                return;
            }
            
            document.getElementById('resultado').innerHTML = '<div class="loading">🔍 Verificando código 2FA...</div>';
            
            $.ajax({
                url: '/api/2fa/verificar-login',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ codigo: codigo }),
                success: function(response) {
                    if (response.success && response.access_token) {
                        localStorage.setItem('token', response.access_token);
                        localStorage.setItem('user', JSON.stringify(response.user));
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    let response = xhr.responseJSON;
                    document.getElementById('resultado').innerHTML = `<div class="error">❌ ${response.message || 'Código incorrecto'}</div>`;
                    document.getElementById('codigo2fa').value = '';
                    document.getElementById('codigo2fa').focus();
                }
            });
        }
        
        // REENVIAR VERIFICACIÓN
        document.getElementById('reenviarLink').onclick = function(e) {
            e.preventDefault();
            document.getElementById('reenviarForm').style.display = 'block';
        }
        
        function cerrarReenviar() {
            document.getElementById('reenviarForm').style.display = 'none';
            document.getElementById('resultadoReenvio').innerHTML = '';
            document.getElementById('emailReenviar').value = '';
        }
        
        function reenviarVerificacion() {
            const email = document.getElementById('emailReenviar').value;
            const resultadoDiv = document.getElementById('resultadoReenvio');
            
            if (!email) {
                resultadoDiv.innerHTML = '<div style="color: red;">❌ Ingresa tu correo electrónico</div>';
                return;
            }
            
            resultadoDiv.innerHTML = '<div style="color: #666;">📧 Enviando...</div>';
            
            fetch('/reenviar-verificacion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ correo: email })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    resultadoDiv.innerHTML = '<div style="color: green;">✅ ' + data.message + '</div>';
                    setTimeout(() => cerrarReenviar(), 3000);
                } else {
                    resultadoDiv.innerHTML = '<div style="color: red;">❌ ' + data.message + '</div>';
                }
            })
            .catch(error => {
                resultadoDiv.innerHTML = '<div style="color: red;">❌ Error al reenviar</div>';
            });
        }
        
        // =========================================================
        // DOCUMENT READY - SIN REDIRECCIÓN AUTOMÁTICA
        // =========================================================
        $(document).ready(function() {
            // Solo cargar el captcha, NO redirigir automáticamente
            refreshCaptcha();
            
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const correo = $('#correo').val();
                const contrasena = $('#contrasena').val();
                const captcha = $('#captcha').val().trim();
                
                if (!captcha) {
                    $('#resultado').html('<div class="error">❌ Por favor ingresa el código de seguridad</div>');
                    return;
                }
                
                if (captcha.toUpperCase() !== currentCaptchaCode) {
                    $('#resultado').html('<div class="error">❌ Código de seguridad incorrecto</div>');
                    refreshCaptcha();
                    return;
                }
                
                $('#resultado').html('<div class="loading">🔍 Verificando credenciales...</div>');
                
                $.ajax({
                    url: '/api/login',
                    method: 'POST',
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify({
                        correo: correo,
                        contrasena: contrasena
                    }),
                    success: function(response) {
                        if (response.success && response.access_token) {
                            localStorage.setItem('token', response.access_token);
                            localStorage.setItem('user', JSON.stringify(response.user));
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;
                        
                        if (response && response.requires_2fa === true) {
                            mostrarPanel2FA(response.user_id);
                            return;
                        }
                        
                        if (response && response.blocked === true) {
                            let minutos = response.minutos || 15;
                            let segundos = minutos * 60;
                            
                            $('#resultado').html(`
                                <div class="blocked">
                                    <strong>🔒 ${response.message}</strong><br>
                                    ${response.details}<br>
                                    <div id="contador">🕐 ${minutos}:00</div>
                                </div>
                            `);
                            
                            const intervalo = setInterval(() => {
                                const mins = Math.floor(segundos / 60);
                                const segs = segundos % 60;
                                $('#contador').text(`🕐 Tiempo restante: ${mins}:${segs.toString().padStart(2, '0')}`);
                                if (segundos <= 0) {
                                    clearInterval(intervalo);
                                    location.reload();
                                }
                                segundos--;
                            }, 1000);
                            refreshCaptcha();
                            
                        } else if (response && response.message) {
                            $('#resultado').html(`<div class="error">❌ ${response.message}</div>`);
                            refreshCaptcha();
                        } else {
                            $('#resultado').html('<div class="error">❌ Error desconocido</div>');
                            refreshCaptcha();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>