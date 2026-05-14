<!DOCTYPE html>
<html>
<head>
    <title>Configurar 2FA - Pet Spa</title>
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
        h2 { text-align: center; color: #333; }
        .qr-code {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
        }
        .qr-code img { max-width: 200px; }
        .secret {
            background: #f0f0f0;
            padding: 10px;
            text-align: center;
            font-family: monospace;
            font-size: 18px;
            border-radius: 5px;
            margin: 10px 0;
            word-break: break-all;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover { transform: translateY(-2px); }
        .error { color: red; background: #ffebee; padding: 10px; border-radius: 8px; margin-top: 10px; }
        .success { color: green; background: #e8f5e9; padding: 10px; border-radius: 8px; margin-top: 10px; }
        .info { background: #e3f2fd; padding: 10px; border-radius: 8px; margin: 15px 0; font-size: 14px; }
        .volver { background: #607d8b; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Configurar Autenticación 2FA</h2>
        
        <div class="info">
            📱 <strong>¿Cómo funciona?</strong><br>
            1. Escanea el código QR con Google Authenticator<br>
            2. Ingresa el código de 6 dígitos que aparece en la app<br>
            3. Listo! Tu cuenta estará más segura
        </div>
        
        <div id="mensaje"></div>
        
        <div id="paso1">
            <div class="qr-code" id="qrDiv">Cargando código QR...</div>
            <div class="secret" id="secretDiv"></div>
            
            <input type="text" id="codigo" placeholder="Código de 6 dígitos" maxlength="6">
            <button onclick="verificar2FA()">✅ Verificar y Activar 2FA</button>
        </div>
        
        <div id="paso2" style="display: none;">
            <div class="success">✅ ¡2FA Activado correctamente!</div>
            <button class="volver" onclick="window.location.href='/admin/dashboard'">Ir al Dashboard</button>
        </div>
    </div>

    <script>
        // Obtener token SOLO de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        
        console.log('Token obtenido de URL:', token ? token.substring(0, 30) + '...' : 'NO HAY TOKEN');
        
        if (!token) {
            document.getElementById('paso1').innerHTML = '<div class="error">⚠️ No hay token. <a href="/admin/ir-2fa">Intentar de nuevo</a></div>';
        } else {
            localStorage.setItem('token', token);
        }
        
        let secretoActual = '';
        
        if (token) {
            fetch('/api/2fa/generar', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    secretoActual = data.secreto;
                    document.getElementById('secretDiv').innerHTML = '<strong>Clave secreta:</strong><br>' + secretoActual;
                    document.getElementById('qrDiv').innerHTML = `<img src="${data.qr}">`;
                } else {
                    document.getElementById('mensaje').innerHTML = `<div class="error">❌ ${data.message}</div>`;
                }
            })
            .catch(error => {
                document.getElementById('mensaje').innerHTML = `<div class="error">❌ Error: ${error.message}</div>`;
            });
        }
        
        function verificar2FA() {
            const codigo = document.getElementById('codigo').value;
            
            if (!codigo || codigo.length !== 6) {
                document.getElementById('mensaje').innerHTML = '<div class="error">❌ Ingresa un código de 6 dígitos</div>';
                return;
            }
            
            fetch('/api/2fa/verificar', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ codigo: codigo, secreto: secretoActual })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('paso1').style.display = 'none';
                    document.getElementById('paso2').style.display = 'block';
                    document.getElementById('mensaje').innerHTML = '';
                } else {
                    document.getElementById('mensaje').innerHTML = `<div class="error">❌ ${data.message}</div>`;
                }
            });
        }
    </script>
</body>
</html>