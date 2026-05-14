<!DOCTYPE html>
<html>
<head>
    <title>Redirigiendo...</title>
    <script>
        // Obtener token de localStorage
        const token = localStorage.getItem('token');
        
        if (token) {
            // Redirigir a la página de 2FA con el token en la URL
            window.location.href = '/admin/configurar-2fa?token=' + token;
        } else {
            // No hay token, ir al login
            window.location.href = '/';
        }
    </script>
</head>
<body>
    <p>Redirigiendo...</p>
</body>
</html>