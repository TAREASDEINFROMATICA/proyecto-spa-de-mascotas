<!DOCTYPE html>
<html>
<head>
    <title>Verificación - Pet Spa</title>
    <style>
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
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 { color: #4CAF50; }
        .error h1 { color: #f44336; }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container @if(!$success) error @endif">
        @if($success)
            <h1>✅ ¡Email Verificado!</h1>
            <p>Tu cuenta ha sido activada correctamente.</p>
            <p>Ya puedes iniciar sesión en Pet Spa.</p>
            <a href="/" class="btn">Iniciar Sesión</a>
        @else
            <h1>❌ Token Inválido</h1>
            <p>El enlace ha expirado o no es válido.</p>
            <p>El token de verificación solo es válido por 15 minutos.</p>
            <a href="/registro" class="btn">Registrarse de nuevo</a>
        @endif
    </div>
</body>
</html>