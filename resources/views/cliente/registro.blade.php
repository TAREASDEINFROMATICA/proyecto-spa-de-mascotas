<!DOCTYPE html>
<html>
<head>
    <title>Registro - Pet Spa</title>
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
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
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
            margin-top: 15px;
        }
        button:hover { transform: translateY(-2px); }
        
        /* Medidor de fuerza */
        .password-strength { margin-top: 5px; }
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
        
        .requirement {
            font-size: 12px;
            margin: 3px 0;
        }
        .requirement.valid { color: #27ae60; }
        .requirement.invalid { color: #e74c3c; }
        
        .error {
            background: #ffebee;
            color: red;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .success {
            background: #e8f5e9;
            color: green;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .login-link a { color: #667eea; text-decoration: none; }
        small {
            color: #666;
            display: block;
            margin-top: -5px;
            margin-bottom: 10px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🐾 Crear cuenta en Pet Spa</h2>
        
        <div id="alert"></div>
        
        <form id="registroForm">
            <!-- Nombres - Solo letras -->
            <input type="text" id="nombres" placeholder="Nombres *" 
                   pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+" 
                   title="Solo letras y espacios" required>
            <small>Solo letras y espacios</small>
            
            <!-- Apellidos - Solo letras -->
            <input type="text" id="apellidos" placeholder="Apellidos *" 
                   pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+" 
                   title="Solo letras y espacios" required>
            <small>Solo letras y espacios</small>
            
            <!-- Email -->
            <input type="email" id="correo" placeholder="Correo electrónico *" required>
            
            <!-- Teléfono - Solo números -->
            <input type="tel" id="telefono" placeholder="Teléfono *" 
                   pattern="[0-9]{8,15}" 
                   title="Solo números, 8 a 15 dígitos" required>
            <small>Solo números, 8 a 15 dígitos</small>
            
            <!-- CI - Solo números (opcional) -->
            <input type="text" id="ci" placeholder="Cédula de Identidad (opcional)" 
                   pattern="[0-9]{6,12}" 
                   title="Solo números, 6 a 12 dígitos">
            <small>Solo números, 6 a 12 dígitos (opcional)</small>
            
            <!-- Dirección -->
            <input type="text" id="direccion" placeholder="Dirección">
            
            <!-- Contraseña -->
            <input type="password" id="contrasena" placeholder="Contraseña *" required>
            <small>Mínimo 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos</small>
            
            <!-- Medidor de fuerza -->
            <div class="password-strength">
                <div class="strength-meter">
                    <div class="strength-meter-fill" id="strengthFill"></div>
                </div>
                <div id="strengthText"></div>
            </div>
            
            <!-- Requisitos -->
            <div id="requisitos">
                <div class="requirement invalid" id="req-length">❌ Mínimo 8 caracteres</div>
                <div class="requirement invalid" id="req-upper">❌ Al menos una mayúscula</div>
                <div class="requirement invalid" id="req-lower">❌ Al menos una minúscula</div>
                <div class="requirement invalid" id="req-number">❌ Al menos un número</div>
                <div class="requirement invalid" id="req-symbol">❌ Al menos un símbolo (@$!%*#?&)</div>
            </div>
            
            <button type="submit">📝 Registrarme</button>
        </form>
        
        <div class="login-link">
            ¿Ya tienes cuenta? <a href="/">Inicia sesión aquí</a>
        </div>
    </div>

    <script>
        // Medidor de fuerza de contraseña
        const password = document.getElementById('contrasena');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        
        function checkPassword() {
            const val = password.value;
            const checks = {
                length: val.length >= 8,
                upper: /[A-Z]/.test(val),
                lower: /[a-z]/.test(val),
                number: /[0-9]/.test(val),
                symbol: /[@$!%*#?&]/.test(val)
            };
            
            // Actualizar requisitos
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
            
            // Calcular fuerza
            let strength = 0;
            if (checks.length) strength++;
            if (checks.upper && checks.lower) strength++;
            if (checks.number) strength++;
            if (checks.symbol) strength++;
            
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
        
        // Validación en tiempo real de los campos
        function validarCampo(input, pattern) {
            const valor = input.value;
            if (pattern && valor && !new RegExp(pattern).test(valor)) {
                input.style.borderColor = '#e74c3c';
                return false;
            } else {
                input.style.borderColor = '#ddd';
                return true;
            }
        }
        
        // Validar teléfono
        document.getElementById('telefono').addEventListener('input', function() {
            validarCampo(this, '^[0-9]{8,15}$');
        });
        
        // Validar CI
        document.getElementById('ci').addEventListener('input', function() {
            if (this.value) {
                validarCampo(this, '^[0-9]{6,12}$');
            } else {
                this.style.borderColor = '#ddd';
            }
        });
        
        // Validar nombres
        document.getElementById('nombres').addEventListener('input', function() {
            validarCampo(this, '^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\\s]+$');
        });
        
        document.getElementById('apellidos').addEventListener('input', function() {
            validarCampo(this, '^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\\s]+$');
        });
        
        password.addEventListener('input', checkPassword);
        
        // Enviar formulario
        $('#registroForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validaciones extra antes de enviar
            const nombres = document.getElementById('nombres');
            const apellidos = document.getElementById('apellidos');
            const telefono = document.getElementById('telefono');
            const ci = document.getElementById('ci');
            
            if (!validarCampo(nombres, '^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\\s]+$')) {
                $('#alert').html('<div class="error">❌ Los nombres solo pueden contener letras y espacios</div>');
                return;
            }
            
            if (!validarCampo(apellidos, '^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\\s]+$')) {
                $('#alert').html('<div class="error">❌ Los apellidos solo pueden contener letras y espacios</div>');
                return;
            }
            
            if (!validarCampo(telefono, '^[0-9]{8,15}$')) {
                $('#alert').html('<div class="error">❌ El teléfono debe contener solo números (8 a 15 dígitos)</div>');
                return;
            }
            
            if (ci.value && !validarCampo(ci, '^[0-9]{6,12}$')) {
                $('#alert').html('<div class="error">❌ La cédula debe contener solo números (6 a 12 dígitos)</div>');
                return;
            }
            
            if (!checkPassword()) {
                $('#alert').html('<div class="error">❌ La contraseña no cumple con los requisitos</div>');
                return;
            }
            
            $('#alert').html('<div class="success">⏳ Procesando registro...</div>');
            
            $.ajax({
                url: '/api/registro',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                contentType: 'application/json',
                data: JSON.stringify({
                    nombres: $('#nombres').val(),
                    apellidos: $('#apellidos').val(),
                    correo: $('#correo').val(),
                    ci: $('#ci').val(),
                    telefono: $('#telefono').val(),
                    direccion: $('#direccion').val(),
                    contrasena: $('#contrasena').val()
                }),
                success: function(response) {
                    $('#alert').html(`<div class="success">✅ ${response.message}</div>`);
                    $('#registroForm')[0].reset();
                    strengthFill.className = 'strength-meter-fill';
                    strengthText.innerHTML = '';
                },
                error: function(xhr) {
                    let msg = 'Error en el registro';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    $('#alert').html(`<div class="error">❌ ${msg}</div>`);
                }
            });
        });
    </script>
</body>
</html>