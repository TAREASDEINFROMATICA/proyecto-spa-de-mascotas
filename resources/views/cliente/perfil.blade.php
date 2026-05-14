<!DOCTYPE html>
<html>
<head>
    <title>Mi Perfil - Pet Spa</title>
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
        h3 { color: #555; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px; }
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
        }
        button:hover { transform: translateY(-2px); }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .volver {
            background: #607d8b;
            margin-top: 10px;
        }
        .volver:hover { background: #455a64; }
        .info {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        
        /* Medidor de fuerza */
        .password-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #eee;
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
        .btn-password {
            background: #FF9800;
            margin-top: 10px;
        }
        .btn-password:hover { background: #e68900; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🐾 Mi Perfil</h2>
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        <div class="info">
            📧 <strong>Email:</strong> {{ $user->correo }} (no se puede cambiar)
        </div>
        
        @php
            $token = request()->query('token') ?: '';
        @endphp

        <!-- FORMULARIO PARA ACTUALIZAR DATOS PERSONALES -->
        <form method="POST" action="/cliente/perfil?token={{ $token }}">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="text" name="nombres" value="{{ old('nombres', $user->nombres) }}" placeholder="Nombres *" required>
            <input type="text" name="apellidos" value="{{ old('apellidos', $user->apellidos) }}" placeholder="Apellidos *" required>
            <input type="text" name="ci" value="{{ old('ci', $user->ci) }}" placeholder="Cédula de Identidad">
            <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}" placeholder="Teléfono *" required>
            <input type="text" name="direccion" value="{{ old('direccion', $cliente->direccion ?? '') }}" placeholder="Dirección">
            
            <button type="submit">💾 Guardar Cambios</button>
        </form>
        
        <!-- ========================================================= -->
        <!-- SECCIÓN PARA CAMBIAR CONTRASEÑA -->
        <!-- ========================================================= -->
        <div class="password-section">
            <h3>🔒 Cambiar Contraseña</h3>
            
            <div id="passwordAlert"></div>
            
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
                <button type="submit" class="btn-password">🔐 Cambiar Contraseña</button>
            </form>
            <div id="passwordResultado"></div>
        </div>
        
        <button class="volver" onclick="window.location.href='/cliente/dashboard?token={{ $token }}'">← Volver al Dashboard</button>
    </div>

    <script>
        const token = '{{ $token }}';
        
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
                $('#passwordResultado').html('<div class="error" style="margin-top: 10px;">❌ La contraseña no cumple con los requisitos de seguridad.</div>');
                return;
            }
            
            if (nuevaPassword !== confirmacion) {
                $('#passwordResultado').html('<div class="error" style="margin-top: 10px;">❌ Las contraseñas no coinciden.</div>');
                return;
            }
            
            $('#passwordResultado').html('<div class="info" style="margin-top: 10px;">⏳ Procesando...</div>');
            
            $.ajax({
                url: '/cambiar-contrasena',
                method: 'POST',
                headers: { 
                    'Authorization': 'Bearer ' + localStorage.getItem('token'),
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({
                    contrasena_actual: $('#contrasena_actual').val(),
                    contrasena_nueva: nuevaPassword,
                    contrasena_nueva_confirmation: confirmacion
                }),
                success: function(response) {
                    $('#passwordResultado').html('<div class="success" style="margin-top: 10px;">✅ ' + response.message + '</div>');
                    $('#cambiarPasswordForm')[0].reset();
                    $('#strengthFill').css('width', '0%');
                    $('#strengthText').html('');
                    // Resetear requisitos
                    $('.requirement').each(function() {
                        $(this).removeClass('valid').addClass('invalid');
                        $(this).html($(this).html().replace('✅', '❌'));
                    });
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Error al cambiar contraseña';
                    $('#passwordResultado').html('<div class="error" style="margin-top: 10px;">❌ ' + msg + '</div>');
                }
            });
        });
    </script>
</body>
</html>