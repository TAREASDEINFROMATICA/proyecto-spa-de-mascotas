<!DOCTYPE html>
<html>
<head>
    <title>Nueva Venta - Pet Spa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; border: none; cursor: pointer; }
        .btn-primary { background: white; color: #4CAF50; }
        .btn-primary:hover { transform: translateY(-2px); }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .btn-danger { background: #f44336; color: white; }
        .btn-danger:hover { background: #d32f2f; transform: translateY(-2px); }
        .btn-success { background: #4CAF50; color: white; }
        .btn-success:hover { background: #45a049; transform: translateY(-2px); }
        .content { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px; }
        label i { margin-right: 8px; color: #4CAF50; }
        select, input { width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s; font-family: 'Inter', sans-serif; }
        select:focus, input:focus { outline: none; border-color: #4CAF50; box-shadow: 0 0 0 3px rgba(76,175,80,0.1); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .card { background: #f8f9fa; border-radius: 16px; padding: 20px; margin-bottom: 20px; }
        .card h3 { margin-bottom: 15px; color: #333; display: flex; align-items: center; gap: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #e9ecef; padding: 12px; text-align: left; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
        .producto-row:hover { background: #f1f3f5; }
        .btn-eliminar { background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 8px; cursor: pointer; font-size: 12px; }
        .btn-eliminar:hover { background: #c82333; }
        .total-display { text-align: right; font-size: 24px; font-weight: 700; color: #4CAF50; margin-top: 20px; padding-top: 20px; border-top: 2px solid #e0e0e0; }
        .error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .success { background: #d4edda; color: #155724; padding: 12px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .producto-buscar { display: flex; gap: 10px; align-items: flex-end; }
        .producto-buscar select { flex: 2; }
        .producto-buscar input { width: 120px; }
        @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } .header { flex-direction: column; text-align: center; } }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> Nueva Venta</h1>
            <div>
                <a href="/admin/ventas?token={{ $token }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
            </div>
        </div>
        
        <div class="content">
            @if(session('error'))
                <div class="error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif
            
            @if($errors->any())
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.ventas.store', ['token' => $token]) }}" id="ventaForm">
                @csrf
                
                <div class="grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Cliente *</label>
                        <select name="id_cliente" id="id_cliente" required>
                            <option value="">-- Seleccionar cliente --</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id_cliente }}">
                                    {{ $cliente->usuario->nombres }} {{ $cliente->usuario->apellidos }} - {{ $cliente->usuario->correo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
    <label><i class="fas fa-credit-card"></i> Método de Pago *</label>
    <select name="id_metodo_pago" id="id_metodo_pago" required>
        <option value="">-- Seleccionar método --</option>
        @if(isset($metodosPago) && $metodosPago->count() > 0)
            @foreach($metodosPago as $metodo)
                <option value="{{ $metodo->id_metodo_pago }}">{{ $metodo->nombre }}</option>
            @endforeach
        @else
            <option value="" disabled>No hay métodos de pago disponibles</option>
        @endif
    </select>
</div>
                </div>
                
                <!-- Productos -->
                <div class="card">
                    <h3><i class="fas fa-boxes"></i> Agregar Productos</h3>
                    <div class="producto-buscar">
                        <select id="producto_select" style="flex: 2;">
    <option value="">-- Seleccionar producto --</option>
    @foreach($productos as $producto)
        <option value="{{ $producto->id_producto_venta }}" 
                data-precio="{{ $producto->precio_venta }}"
                data-stock="{{ $producto->stock }}">
            {{ $producto->nombre }} - Bs {{ number_format($producto->precio_venta, 2) }} (Stock: {{ $producto->stock }})
        </option>
    @endforeach
</select>
                        <input type="number" id="producto_cantidad" placeholder="Cantidad" min="1" value="1" style="width: 120px;">
                        <button type="button" id="btnAgregarProducto" class="btn btn-primary" style="padding: 12px 20px;"><i class="fas fa-plus"></i> Agregar</button>
                    </div>
                </div>
                
                <!-- Tabla de productos agregados -->
                <div class="card">
                    <h3><i class="fas fa-shopping-cart"></i> Productos en la Venta</h3>
                    <table id="tablaProductos">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProductos">
                            <tr>
                                <td colspan="5" style="text-align: center; color: #999;">No hay productos agregados</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="total-display">
                        TOTAL: <span id="totalVenta">0.00</span> Bs
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-money-bill"></i> Monto Recibido *</label>
                    <input type="number" name="monto_pagado" id="monto_pagado" step="0.01" min="0" required placeholder="0.00">
                </div>
                
                <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" onclick="registrarVenta()" class="btn btn-success" style="padding: 15px 30px; font-size: 16px;">
                        <i class="fas fa-check-circle"></i> Registrar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        const token = '{{ $token }}';
        let productosAgregados = [];
        
        // Cargar productos para el select
        
        
        // Agregar producto
        function agregarProducto() {
    const select = document.getElementById('producto_select');
    const selectedOption = select.options[select.selectedIndex];
    const productoId = select.value;
    const cantidad = parseInt(document.getElementById('producto_cantidad').value);
    
    if (!productoId) {
        alert('❌ Selecciona un producto');
        return;
    }
    
    if (!cantidad || cantidad <= 0) {
        alert('❌ Ingresa una cantidad válida');
        return;
    }
    
    // Obtener datos del option seleccionado
    const nombre = selectedOption.text.split(' - ')[0];
    const precio = parseFloat(selectedOption.getAttribute('data-precio'));
    const stock = parseInt(selectedOption.getAttribute('data-stock'));
    
    if (cantidad > stock) {
        alert(`❌ Stock insuficiente. Solo hay ${stock} unidades`);
        return;
    }
    
    // Verificar si ya existe
    const existe = productosAgregados.find(p => p.id === productoId);
    if (existe) {
        alert('❌ Este producto ya está agregado. Elimínalo y vuelve a agregar si necesitas más cantidad.');
        return;
    }
    
    productosAgregados.push({
        id: productoId,
        nombre: nombre,
        precio: precio,
        cantidad: cantidad,
        stock: stock
    });
    
    actualizarTabla();
    
    // Limpiar select
    select.value = '';
    document.getElementById('producto_cantidad').value = 1;
}
        
        // Actualizar tabla
        function actualizarTabla() {
            const tbody = document.getElementById('tbodyProductos');
            let total = 0;
            
            if (productosAgregados.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #999;">No hay productos agregados</td></tr>';
                document.getElementById('totalVenta').innerText = '0.00';
                return;
            }
            
            tbody.innerHTML = '';
            productosAgregados.forEach((p, index) => {
                const subtotal = p.precio * p.cantidad;
                total += subtotal;
                
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td>${p.nombre}</td>
                    <td>Bs ${p.precio.toFixed(2)}</td>
                    <td>${p.cantidad}</td>
                    <td>Bs ${subtotal.toFixed(2)}</td>
                    <td><button type="button" class="btn-eliminar" onclick="eliminarProducto(${index})"><i class="fas fa-trash"></i> Eliminar</button></td>
                `;
            });
            
            document.getElementById('totalVenta').innerText = total.toFixed(2);
        }
        
        // Eliminar producto
        function eliminarProducto(index) {
            productosAgregados.splice(index, 1);
            actualizarTabla();
        }
        
        // Registrar venta
        function registrarVenta() {
            const clienteId = document.getElementById('id_cliente').value;
            const metodoPago = document.getElementById('id_metodo_pago').value;
            const montoPagado = parseFloat(document.getElementById('monto_pagado').value);
            const totalVenta = parseFloat(document.getElementById('totalVenta').innerText);
            
            if (!clienteId) {
                alert('❌ Selecciona un cliente');
                return;
            }
            
            if (!metodoPago) {
                alert('❌ Selecciona un método de pago');
                return;
            }
            
            if (productosAgregados.length === 0) {
                alert('❌ Agrega al menos un producto');
                return;
            }
            
            if (isNaN(montoPagado) || montoPagado < totalVenta) {
                alert(`❌ El monto pagado (Bs ${montoPagado}) debe ser al menos el total de la venta (Bs ${totalVenta})`);
                return;
            }
            
            const productosEnviar = productosAgregados.map(p => ({
                id_producto: p.id,
                cantidad: p.cantidad
            }));
            
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            
            fetch('/admin/ventas?token=' + token, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id_cliente: clienteId,
                    id_metodo_pago: metodoPago,
                    monto_pagado: montoPagado,
                    productos: productosEnviar
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Venta registrada correctamente');
                    if (data.venta_id) {
                        window.location.href = '/admin/ventas/' + data.venta_id + '?token=' + token;
                    } else {
                        window.location.href = '/admin/ventas?token=' + token;
                    }
                } else {
                    alert('❌ Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> Registrar Venta';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al registrar la venta');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Registrar Venta';
            });
        }
        
        // Event listeners
        document.getElementById('btnAgregarProducto').addEventListener('click', agregarProducto);
        
    </script>
</body>
</html>