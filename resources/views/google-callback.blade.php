<!DOCTYPE html>
<html>
<head>
    <title>Redirigiendo - Pet Spa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
        }
        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4CAF50;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error {
            color: red;
            background: #ffebee;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h3>🔄 Procesando autenticación...</h3>
        <div id="mensaje"></div>
    </div>

    <script>
        // Obtener token de la URL
        const urlParams = new URLSearchParams(window.location.search);
        let token = urlParams.get('token');
        
        // Si no hay token, buscar en la URL completa
        if (!token && window.location.hash) {
            const hashParams = new URLSearchParams(window.location.hash.substring(1));
            token = hashParams.get('token');
        }
        
        console.log('Token obtenido:', token ? 'SI' : 'NO');
        
        if (token) {
            // Guardar token en localStorage
            localStorage.setItem('token', token);
            
            // Obtener usuario para saber su rol
            fetch('/api/me', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.user) {
                    localStorage.setItem('user', JSON.stringify(data.user));
                    
                    // Redirigir según el rol
                    if (data.user.rol === 'Administrador') {
                        window.location.href = '/admin/dashboard?token=' + token;
                    } else if (data.user.rol === 'Cliente') {
                        window.location.href = '/cliente/dashboard?token=' + token;
                    } else {
                        window.location.href = '/personal/dashboard?token=' + token;
                    }
                } else {
                    throw new Error('No se pudo obtener el usuario');
                }
            })
            .catch(error => {
                document.getElementById('mensaje').innerHTML = '<div class="error">❌ Error al cargar usuario. <a href="/">Volver al login</a></div>';
            });
        } else {
            // No hay token, redirigir al login
            document.getElementById('mensaje').innerHTML = '<div class="error">❌ No se recibió token de autenticación. <a href="/">Volver al login</a></div>';
            setTimeout(() => {
                window.location.href = '/';
            }, 3000);
        }
    </script>
</body>
</html>