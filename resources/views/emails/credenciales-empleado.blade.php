<!DOCTYPE html>
<html>
<head>
    <title>Tus credenciales - Pet Spa</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>🐾 ¡Bienvenido al equipo de Pet Spa!</h2>
    
    <p>Hola <strong>{{ $usuario->nombres }} {{ $usuario->apellidos }}</strong>,</p>
    
    <p>Has sido registrado como <strong>{{ $usuario->rol->nombre }}</strong> en el sistema Pet Spa.</p>
    
    <p>Tus credenciales de acceso son:</p>
    <p><strong>Este enlace expira en 15 minutos.</strong></p>
    
    <ul style="background: #f5f5f5; padding: 15px; border-radius: 8px;">
        <li><strong>📧 Correo:</strong> {{ $usuario->correo }}</li>
        <li><strong>🔒 Contraseña:</strong> {{ $contrasena }}</li>
    </ul>
    
    <p style="margin: 20px 0;">
       <a href="{{ url('/') }}" style="background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            🔐 Iniciar Sesión
        </a>
    </p>
    
    <p>⚠️ <strong>Recomendación:</strong> Después de iniciar sesión, cambia tu contraseña por una personalizada.</p>
    
    <hr>
    
    <p style="font-size: 12px; color: #666;">Pet Spa - Cuidando a tu mejor amigo 🐕🐈</p>
</body>
</html>