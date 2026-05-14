<!DOCTYPE html>
<html>
<head>
    <title>Verifica tu cuenta</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>🐾 ¡Bienvenido a Pet Spa!</h2>
    
    <p>Hola <strong>{{ $usuario->nombres }} {{ $usuario->apellidos }}</strong>,</p>
    
    <p>Gracias por registrarte. Para activar tu cuenta, haz clic en el siguiente enlace:</p>
    
    <p style="margin: 20px 0;">
        <a href="{{ $enlace }}" style="background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            ✅ Verificar mi cuenta
        </a>
    </p>
    
    <p><strong>Este enlace expira en 15 minutos.</strong></p>
    
    <p>Si no solicitaste este registro, ignora este mensaje.</p>
    
    <hr>
    
    <p style="font-size: 12px; color: #666;">Pet Spa - Cuidando a tu mejor amigo 🐕🐈</p>
</body>
</html>