<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Citas - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; background: white; color: #2196F3; }
        .content { padding: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .estado-reservado { background: #fff3e0; color: #ff9800; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; }
        .estado-programado { background: #e8f5e9; color: #4CAF50; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; }
        .estado-concluido { background: #e3f2fd; color: #2196F3; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; }
        .filtros { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
        .filtros input, .filtros select { padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calendar-alt"></i> Gestión de Citas</h1>
            <a href="/admin/dashboard?token={{ $token }}" class="btn"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
        <div class="content">
            <div class="filtros">
                <input type="date" id="filtroFecha" placeholder="Filtrar por fecha">
                <select id="filtroEstado">
                    <option value="">Todos los estados</option>
                    <option value="reservado">Pendiente</option>
                    <option value="programado">Confirmada</option>
                    <option value="concluido">Concluida</option>
                </select>
                <button onclick="aplicarFiltros()" class="btn" style="background: #4CAF50; color: white;">Filtrar</button>
            </div>
            <div id="citasContainer">Cargando...</div>
        </div>
    </div>
    <script>
        const token = '{{ $token }}';
        function cargarCitas() {
            fetch('/admin/citas/todas?token=' + token)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar citas en tabla
                    }
                });
        }
        cargarCitas();
    </script>
</body>
</html>