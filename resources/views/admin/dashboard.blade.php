<!DOCTYPE html>
<html>
<head>
    <title>Admin - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        
        .header p {
            opacity: 0.9;
            margin-top: 8px;
            font-size: 14px;
        }
        
        .content {
            padding: 30px;
        }
        
        .info-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdef5 100%);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .info-card i {
            font-size: 48px;
            color: #1976D2;
        }
        
        .info-text {
            flex: 1;
        }
        
        .info-text strong {
            font-size: 18px;
            color: #1565C0;
        }
        
        .info-text p {
            color: #555;
            margin-top: 5px;
        }
        
        h2 {
            font-size: 18px;
            color: #333;
            margin: 25px 0 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        h2 i {
            color: #4CAF50;
            font-size: 20px;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 12px;
            margin-bottom: 10px;
        }
        
        .menu-item {
            background: #f8f9fa;
            border-radius: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .menu-item:hover {
            background: #e8f5e9;
            transform: translateX(5px);
        }
        
        .menu-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .menu-item a i {
            width: 28px;
            font-size: 18px;
            color: #4CAF50;
        }
        
        .menu-item a:hover {
            color: #4CAF50;
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(244,67,54,0.3);
        }
        
        hr {
            margin: 20px 0;
            border: none;
            height: 1px;
            background: linear-gradient(to right, #e0e0e0, transparent);
        }
        
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        
        .stat-card { 
            background: white; 
            border-radius: 16px; 
            padding: 20px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            transition: transform 0.3s; 
        }
        
        .stat-card:hover { 
            transform: translateY(-5px); 
        }
        
        .stat-card .icon { 
            font-size: 32px; 
            margin-bottom: 10px; 
        }
        
        .stat-card .value { 
            font-size: 28px; 
            font-weight: 700; 
        }
        
        .stat-card .label { 
            color: #666; 
            font-size: 14px; 
            margin-top: 5px; 
        }
        
        .charts-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); 
            gap: 30px; 
            margin: 30px 0; 
        }
        
        .chart-card { 
            background: #f8f9fa; 
            border-radius: 16px; 
            padding: 20px; 
        }
        
        .chart-card h3 { 
            margin-bottom: 15px; 
            color: #333; 
        }
        
        canvas { 
            max-height: 300px; 
        }
        
        @media (max-width: 640px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }
            .info-card {
                flex-direction: column;
                text-align: center;
            }
            .charts-grid {
                grid-template-columns: 1fr;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-shield-alt"></i>
                Panel de Administrador
            </h1>
            <p>Gestión completa del Spa de Mascotas</p>
        </div>
        
        <div class="content">
            <!-- ========================================================= -->
            <!-- ESTADÍSTICAS - TARJETAS -->
            <!-- ========================================================= -->
            <div class="stats-grid">
                <div class="stat-card" style="border-left: 4px solid #4CAF50;">
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <div class="value">Bs {{ number_format($ventasHoy ?? 0, 2) }}</div>
                    <div class="label">Ventas de hoy</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #4CAF50;">
                    <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="value">Bs {{ number_format($ventasMes ?? 0, 2) }}</div>
                    <div class="label">Ventas del mes</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #4CAF50;">
                    <div class="icon"><i class="fas fa-chart-bar"></i></div>
                    <div class="value">Bs {{ number_format($ventasTotales ?? 0, 2) }}</div>
                    <div class="label">Ventas totales</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #2196F3;">
                    <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="value">{{ $citasHoy ?? 0 }}</div>
                    <div class="label">Citas hoy</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #2196F3;">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <div class="value">{{ $citasPendientes ?? 0 }}</div>
                    <div class="label">Citas pendientes</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #2196F3;">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <div class="value">{{ $citasConfirmadas ?? 0 }}</div>
                    <div class="label">Citas confirmadas</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #2196F3;">
                    <div class="icon"><i class="fas fa-check-double"></i></div>
                    <div class="value">{{ $citasConcluidas ?? 0 }}</div>
                    <div class="label">Citas concluidas</div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #ff9800;">
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="value">{{ ($productosStockBajo ?? 0) + ($insumosStockBajo ?? 0) }}</div>
                    <div class="label">Alertas de stock bajo</div>
                </div>
            </div>

           
            
            <!-- ========================================================= -->
            <!-- INFO USUARIO -->
            <!-- ========================================================= -->
            <div class="info-card">
                <i class="fas fa-user-circle"></i>
                <div class="info-text">
                    <strong id="userName">Cargando...</strong>
                    <p id="userEmail">Cargando datos del usuario...</p>
                    <p id="userRol" style="margin-top: 5px;"></p>
                </div>
            </div>
            
            <!-- ========================================================= -->
            <!-- MENÚ PRINCIPAL -->
            <!-- ========================================================= -->
            <h2><i class="fas fa-tachometer-alt"></i> Gestión Principal</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceEstadisticas"><i class="fas fa-chart-line"></i> Ver estadísticas</a></div>
                <div class="menu-item"><a href="#" id="enlaceEmpleados"><i class="fas fa-users"></i> Gestionar Empleados</a></div>
                <div class="menu-item"><a href="#" id="enlaceServicios"><i class="fas fa-cut"></i> Gestionar Servicios</a></div>
                <div class="menu-item"><a href="#" id="enlaceAgenda"><i class="fas fa-calendar-alt"></i> Agenda Maestra</a></div>
                <div class="menu-item"><a href="#" id="enlaceCitas"><i class="fas fa-calendar-check"></i> Ver todas las citas</a></div>
            </div>
            
            <h2><i class="fas fa-chart-line"></i> Ventas y Finanzas</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceReportes"><i class="fas fa-file-alt"></i> Reportes financieros</a></div>
                <div class="menu-item"><a href="#" id="enlaceVentas"><i class="fas fa-shopping-cart"></i> Gestión de Ventas</a></div>
            </div>
            
            <h2><i class="fas fa-boxes"></i> Inventario</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceInsumos"><i class="fas fa-flask"></i> Gestión de Insumos (Grooming)</a></div>
                <div class="menu-item"><a href="#" id="enlaceProductos"><i class="fas fa-store"></i> Gestión de Productos (Tienda)</a></div>
                <div class="menu-item"><a href="#" id="enlaceCategorias"><i class="fas fa-folder"></i> Gestión de Categorías</a></div>
                <div class="menu-item"><a href="#" id="enlaceAlertasStock"><i class="fas fa-exclamation-triangle"></i> Alertas de Stock Bajo</a></div>
            </div>
            
            <h2><i class="fas fa-user-friends"></i> Gestión de Usuarios</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlaceClientes"><i class="fas fa-user"></i> Gestionar Clientes</a></div>
                <div class="menu-item"><a href="#" id="enlaceMascotas"><i class="fas fa-dog"></i> Todas las Mascotas</a></div>
            </div>
            
            <h2><i class="fas fa-lock"></i> Seguridad</h2>
            <div class="menu-grid">
                <div class="menu-item"><a href="#" id="enlace2fa"><i class="fas fa-key"></i> Configurar 2FA (Google Authenticator)</a></div>
                <div class="menu-item"><a href="#" id="enlaceLogs"><i class="fas fa-history"></i> Ver Logs de Auditoría</a></div>
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
        
        // =========================================================
        // GESTIÓN PRINCIPAL
        // =========================================================
        crearEnlace('enlaceEstadisticas', '/admin/dashboard');
        crearEnlace('enlaceEmpleados', '/admin/empleados');
        crearEnlace('enlaceServicios', '/admin/servicios');
        crearEnlace('enlaceAgenda', '/admin/agenda');
        crearEnlace('enlaceCitas', '/admin/citas/todas');
        
        // =========================================================
        // VENTAS Y FINANZAS
        // =========================================================
        crearEnlace('enlaceReportes', '/admin/reportes-financieros');
        crearEnlace('enlaceVentas', '/admin/ventas');
        
        // =========================================================
        // INVENTARIO
        // =========================================================
        crearEnlace('enlaceInsumos', '/admin/insumos');
        crearEnlace('enlaceProductos', '/admin/productos');
        crearEnlace('enlaceCategorias', '/admin/categorias');
crearEnlace('enlaceAlertasStock', '/admin/alertas-stock');
        
        // =========================================================
        // GESTIÓN DE USUARIOS
        // =========================================================
        crearEnlace('enlaceClientes', '/admin/clientes');
        crearEnlace('enlaceMascotas', '/admin/mascotas');
        
        // =========================================================
        // SEGURIDAD
        // =========================================================
        crearEnlace('enlace2fa', '/admin/configurar-2fa');
        crearEnlace('enlaceLogs', '/admin/logs');
        
        // Cargar datos del usuario
        fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token }
        })
        .then(res => res.json())
        .then(data => {
            if (data.user) {
                document.getElementById('userName').innerHTML = `👋 Bienvenido, ${data.user.nombres} ${data.user.apellidos}`;
                document.getElementById('userEmail').innerHTML = `📧 ${data.user.correo}`;
                document.getElementById('userRol').innerHTML = `<i class="fas fa-tag"></i> Rol: <strong>${data.user.rol.nombre}</strong>`;
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
                window.location.href = '/';
            });
        }
        
        // =========================================================
        // GRÁFICOS
        // =========================================================
      
    </script>
    
    <!-- ========================================================= -->
    <!-- SESIÓN POR INACTIVIDAD -->
    <!-- ========================================================= -->
    <script>
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