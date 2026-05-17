<!DOCTYPE html>
<html>
<head>
    <title>Cliente - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; display: flex; align-items: center; justify-content: center; gap: 12px; }
        .header p { opacity: 0.9; margin-top: 8px; font-size: 14px; }
        .content { padding: 30px; }
        .info-card { background: linear-gradient(135deg, #e3f2fd 0%, #bbdef5 100%); padding: 20px; border-radius: 16px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .info-card i { font-size: 48px; color: #1976D2; }
        .info-text { flex: 1; }
        .info-text strong { font-size: 18px; color: #1565C0; }
        .info-text p { color: #555; margin-top: 5px; }
        h2 { font-size: 18px; color: #333; margin: 25px 0 15px 0; display: flex; align-items: center; gap: 10px; padding-bottom: 8px; border-bottom: 2px solid #e0e0e0; }
        h2 i { color: #FF9800; font-size: 20px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; margin-bottom: 10px; }
        .menu-item { background: #f8f9fa; border-radius: 12px; transition: all 0.3s ease; overflow: hidden; }
        .menu-item:hover { background: #e8f5e9; transform: translateX(5px); }
        .menu-item a { display: flex; align-items: center; gap: 12px; padding: 14px 18px; text-decoration: none; color: #333; font-weight: 500; transition: all 0.3s; }
        .menu-item a i { width: 28px; font-size: 18px; color: #FF9800; }
        .menu-item a:hover { color: #FF9800; }
        .cart-badge { background: #f44336; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold; margin-left: 8px; }
        .btn-logout { background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white; border: none; padding: 14px 24px; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 20px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-logout:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(244,67,54,0.3); }
        @media (max-width: 640px) { .menu-grid { grid-template-columns: 1fr; } .info-card { flex-direction: column; text-align: center; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-paw"></i> Panel del Cliente</h1>
            <p>Gestiona tus mascotas, citas y compras</p>
        </div>
        
        <div class="content">
            <div class="info-card">
                <i class="fas fa-user-circle"></i>
                <div class="info-text">
                    <strong id="userName">Cargando...</strong>
                    <p id="userEmail">Cargando datos del usuario...</p>
                </div>
            </div>
            
            <h2><i class="fas fa-calendar-alt"></i> Citas</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceSolicitarCita"><i class="fas fa-plus-circle"></i> Solicitar Cita</a></div>
                <div class="menu-item"><a href="#" id="enlaceMisCitas"><i class="fas fa-calendar-check"></i> Mis Citas</a></div>
                <div class="menu-item"><a href="#" id="enlaceHistorialServicios"><i class="fas fa-history"></i> Historial de Servicios</a></div>
            </div>
            
            <h2><i class="fas fa-dog"></i> Mis Mascotas</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceMascotas"><i class="fas fa-paw"></i> Ver mis mascotas</a></div>
                <div class="menu-item"><a href="#" id="enlaceRegistrarMascota"><i class="fas fa-plus"></i> Registrar Nueva Mascota</a></div>
            </div>
            
            <h2><i class="fas fa-star"></i> Calificaciones</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceCalificar"><i class="fas fa-star"></i> Calificar Servicios</a></div>
            </div>
            
            <h2><i class="fas fa-store"></i> Tienda</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceTienda"><i class="fas fa-shopping-bag"></i> Tienda</a></div>
                <div class="menu-item"><a href="#" id="enlaceCarrito"><i class="fas fa-shopping-cart"></i> Mi Carrito <span id="carritoCount" class="cart-badge">0</span></a></div>
                <div class="menu-item"><a href="#" id="enlaceMisCompras"><i class="fas fa-receipt"></i> Mis Compras</a></div>
            </div>
            
            <h2><i class="fas fa-user-cog"></i> Mi Cuenta</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlacePerfil"><i class="fas fa-user-edit"></i> Actualizar mis datos</a></div>
                <div class="menu-item"><a href="#" id="enlaceCambiarPassword"><i class="fas fa-key"></i> Cambiar Contraseña</a></div>
            </div>
            
            <button onclick="logout()" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        
        if (!token) {
            window.location.href = '/';
        }
        
        // Función para crear enlaces con token
        function crearEnlace(id, url) {
            const enlace = document.getElementById(id);
            if (enlace) {
                enlace.href = url + '?token=' + token;
                console.log(`Enlace creado: ${id} -> ${enlace.href}`);
            } else {
                console.warn(`Elemento no encontrado: ${id}`);
            }
        }
        
        // Función para actualizar contador del carrito
        function actualizarCarritoCount() {
            const carrito = localStorage.getItem('carrito');
            const items = carrito ? JSON.parse(carrito) : [];
            const totalItems = items.reduce((sum, item) => sum + item.cantidad, 0);
            const badge = document.getElementById('carritoCount');
            if (badge) {
                badge.textContent = totalItems;
                badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
            }
        }
        
        // =========================================================
        // CITAS
        // =========================================================
        crearEnlace('enlaceSolicitarCita', '/cliente/solicitar-cita');
        crearEnlace('enlaceMisCitas', '/cliente/mis-citas');
        crearEnlace('enlaceHistorialServicios', '/cliente/mis-citas');
        
        // =========================================================
        // MASCOTAS
        // =========================================================
        crearEnlace('enlaceMascotas', '/cliente/mascotas');
        crearEnlace('enlaceRegistrarMascota', '/cliente/mascotas/create');
        
        // =========================================================
        // CALIFICACIONES
        // =========================================================
        crearEnlace('enlaceCalificar', '/cliente/mis-citas');
        
        // =========================================================
        // TIENDA
        // =========================================================
        crearEnlace('enlaceTienda', '/cliente/tienda');
        crearEnlace('enlaceCarrito', '/cliente/carrito');
        crearEnlace('enlaceMisCompras', '/cliente/mis-compras');
        
        // =========================================================
        // MI CUENTA
        // =========================================================
        crearEnlace('enlacePerfil', '/cliente/perfil');
        crearEnlace('enlaceCambiarPassword', '/cliente/perfil');
        
        // Actualizar contador del carrito
        actualizarCarritoCount();
        
        // Escuchar cambios en localStorage (para actualizar badge en tiempo real)
        window.addEventListener('storage', function(e) {
            if (e.key === 'carrito') {
                actualizarCarritoCount();
            }
        });
        
        // Cargar datos del usuario
        fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(res => res.json())
        .then(data => {
            if (data.user) {
                document.getElementById('userName').innerHTML = `👋 Bienvenido, ${data.user.nombres} ${data.user.apellidos}`;
                document.getElementById('userEmail').innerHTML = `📧 ${data.user.correo}`;
            } else {
                document.querySelector('.info-card').innerHTML = '<span style="color: red;">❌ Error al cargar datos</span>';
            }
        })
        .catch(() => {
            window.location.href = '/';
        });

        function logout() {
            fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                }
            }).finally(() => {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                localStorage.removeItem('carrito');
                window.location.href = '/';
            });
        }
        
        // =========================================================
        // SESIÓN POR INACTIVIDAD
        // =========================================================
        let tiempoInactividad;
        const TIEMPO_LIMITE = 30 * 60 * 1000; // 30 minutos
        
        function cerrarSesionPorInactividad() {
            const tokenActual = localStorage.getItem('token');
            if (tokenActual) {
                fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + tokenActual,
                        'Content-Type': 'application/json'
                    }
                }).finally(() => {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    localStorage.removeItem('carrito');
                    window.location.href = '/';
                });
            } else {
                window.location.href = '/';
            }
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