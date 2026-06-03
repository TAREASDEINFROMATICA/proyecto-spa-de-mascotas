<!DOCTYPE html>
<html>
<head>
    <title>Mi Carrito - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); 
            min-height: 100vh; 
            padding: 40px 20px; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 28px; 
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35); 
            overflow: hidden; 
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            color: white;
            padding: 30px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header h1 i { font-size: 32px; color: #ffd700; }
        
        /* Botones */
        .btn {
            padding: 10px 20px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary { background: white; color: #ff9800; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }
        .btn-danger { background: #f44336; color: white; }
        .btn-danger:hover { background: #d32f2f; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(244,67,54,0.3); }
        .btn-success { background: #4CAF50; color: white; }
        .btn-success:hover { background: #45a049; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(76,175,80,0.3); }
        
        .content { padding: 35px; }
        
        /* Tabla */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: white;
            margin-bottom: 25px;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #f8fafc;
            padding: 16px 15px;
            text-align: left;
            font-weight: 700;
            font-size: 13px;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
        }
        tr:hover td {
            background: #faf5ff;
        }
        
        .producto-nombre { font-weight: 600; color: #1e293b; }
        .producto-precio { color: #4CAF50; font-weight: 700; }
        .cantidad-input {
            width: 70px;
            padding: 8px 10px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
        }
        .cantidad-input:focus {
            outline: none;
            border-color: #ff9800;
        }
        
        .total {
            text-align: right;
            font-size: 22px;
            font-weight: 700;
            margin: 20px 0;
            padding: 20px 0;
            border-top: 2px solid #e2e8f0;
        }
        .total span {
            font-size: 32px;
            color: #4CAF50;
        }
        
        /* Método de pago */
        .metodo-pago {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 20px;
            padding: 25px;
            margin: 20px 0;
        }
        .metodo-pago label {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }
        .metodo-pago select {
            width: 100%;
            padding: 14px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        .metodo-pago select:focus {
            outline: none;
            border-color: #ff9800;
        }
        
        .pago-detalle {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
        }
        .pago-detalle.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        
        .pago-detalle input {
            width: 100%;
            padding: 12px 14px;
            margin: 8px 0;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .pago-detalle input:focus {
            outline: none;
            border-color: #ff9800;
        }
        
        .bank-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdef5 100%);
            padding: 15px;
            border-radius: 14px;
            margin-bottom: 15px;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .qr-code {
            text-align: center;
            padding: 20px;
        }
        .qr-code img {
            width: 160px;
            height: 160px;
            background: white;
            padding: 12px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .dato-ejemplo {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
            display: block;
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 20px;
        }
        .empty-cart i {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 15px;
            display: block;
        }
        .empty-cart h3 { color: #1e293b; margin-bottom: 10px; }
        .empty-cart p { color: #64748b; margin-bottom: 20px; }
        
        .acciones {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .header { flex-direction: column; text-align: center; }
            .content { padding: 20px; }
            th, td { padding: 10px; font-size: 12px; }
            .cantidad-input { width: 55px; }
            .acciones { flex-direction: column; }
            .btn { justify-content: center; }
        }
        .btn-whatsapp {
    background: #25D366;
    color: white;
}
.btn-whatsapp:hover {
    background: #128C7E;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37,211,102,0.3);
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> Mi Carrito</h1>
            <button class="btn btn-whatsapp" onclick="enviarPedidoWhatsApp()">
    <i class="fab fa-whatsapp"></i> Enviar pedido por WhatsApp
</button>
            <a href="/cliente/tienda?token={{ $token }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Seguir Comprando
            </a>
        </div>
        
        <div class="content">
            <div id="carritoContainer">
                <div style="text-align: center; padding: 60px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 40px; color: #ff9800;"></i>
                    <p style="margin-top: 15px; color: #64748b;">Cargando carrito...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
        
        function getCarrito() {
            const carrito = localStorage.getItem('carrito');
            return carrito ? JSON.parse(carrito) : [];
        }
        
        function guardarCarrito(carrito) {
            localStorage.setItem('carrito', JSON.stringify(carrito));
        }
        
        function actualizarCantidad(id, nuevaCantidad) {
            let carrito = getCarrito();
            const item = carrito.find(i => i.id === id);
            
            if (nuevaCantidad <= 0) {
                carrito = carrito.filter(i => i.id !== id);
            } else {
                if (nuevaCantidad > item.stock) {
                    alert('❌ Stock insuficiente. Solo hay ' + item.stock + ' unidades');
                    return;
                }
                item.cantidad = nuevaCantidad;
            }
            
            guardarCarrito(carrito);
            renderizarCarrito();
        }
        
        function eliminarProducto(id) {
            let carrito = getCarrito();
            carrito = carrito.filter(i => i.id !== id);
            guardarCarrito(carrito);
            renderizarCarrito();
        }
        
        function vaciarCarrito() {
            if (confirm('¿Estás seguro de vaciar el carrito?')) {
                localStorage.removeItem('carrito');
                renderizarCarrito();
            }
        }
        
        function mostrarPagoDetalle() {
            const metodo = document.getElementById('metodoPago').value;
            
            document.getElementById('detalleEfectivo').classList.remove('active');
            document.getElementById('detalleTransferencia').classList.remove('active');
            document.getElementById('detalleQR').classList.remove('active');
            document.getElementById('detalleTarjeta').classList.remove('active');
            
            if (metodo === '1') {
                document.getElementById('detalleEfectivo').classList.add('active');
            } else if (metodo === '2') {
                document.getElementById('detalleTransferencia').classList.add('active');
                document.getElementById('cuentaTransferencia').value = '12345678901234';
            } else if (metodo === '3') {
                document.getElementById('detalleQR').classList.add('active');
            } else if (metodo === '4') {
                document.getElementById('detalleTarjeta').classList.add('active');
                document.getElementById('numeroTarjeta').value = '4111111111111111';
                document.getElementById('fechaVencimiento').value = '12/28';
                document.getElementById('cvv').value = '123';
                document.getElementById('nombreTarjeta').value = 'Cliente Test';
            }
        }
        
        function validarPago() {
            const metodo = document.getElementById('metodoPago').value;
            
            if (metodo === '2') {
                const cuenta = document.getElementById('cuentaTransferencia').value;
                if (!cuenta) {
                    alert('❌ Ingresa el número de cuenta bancaria');
                    return false;
                }
            } else if (metodo === '4') {
                const tarjeta = document.getElementById('numeroTarjeta').value;
                if (!tarjeta || tarjeta.length < 10) {
                    alert('❌ Ingresa un número de tarjeta válido (mínimo 10 dígitos)');
                    return false;
                }
            }
            return true;
        }
        
        function comprarAhora() {
            const metodoPago = document.getElementById('metodoPago').value;
            const carrito = getCarrito();
            
            if (!metodoPago) {
                alert('❌ Selecciona un método de pago');
                return;
            }
            
            if (!validarPago()) {
                return;
            }
            
            if (carrito.length === 0) {
                alert('❌ Tu carrito está vacío');
                return;
            }
            
            const productosEnviar = carrito.map(item => ({
                id_producto: item.id,
                cantidad: item.cantidad
            }));
            
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            
            fetch('/cliente/procesar-pago?token=' + token, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ 
                    id_metodo_pago: metodoPago,
                    productos: productosEnviar
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem('carrito');
                    alert('✅ ' + data.message);
                    window.location.href = '/cliente/mis-compras?token=' + token;
                } else {
                    alert('❌ Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-credit-card"></i> Confirmar Compra';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al procesar el pago');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-credit-card"></i> Confirmar Compra';
            });
        }
        
        function renderizarCarrito() {
            const carrito = getCarrito();
            const container = document.getElementById('carritoContainer');
            
            if (carrito.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Tu carrito está vacío</h3>
                        <p>¡Agrega productos para comenzar tu compra!</p>
                        <a href="/cliente/tienda?token=${token}" class="btn btn-primary" style="display: inline-block;">
                            <i class="fas fa-store"></i> Ir a la tienda
                        </a>
                    </div>
                `;
                return;
            }
            
            let total = 0;
            let html = `
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            carrito.forEach(item => {
                const subtotal = item.precio * item.cantidad;
                total += subtotal;
                
                html += `
                    <tr>
                        <td class="producto-nombre"><i class="fas fa-box" style="color: #ff9800; margin-right: 8px;"></i> ${item.nombre}</td>
                        <td class="producto-precio">Bs ${item.precio.toFixed(2)}</td>
                        <td>
                            <input type="number" class="cantidad-input" value="${item.cantidad}" min="1" max="${item.stock}" 
                                   onchange="actualizarCantidad(${item.id}, parseInt(this.value))">
                        </td>
                        <td class="producto-precio">Bs ${subtotal.toFixed(2)}</td>
                        <td>
                            <button class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="eliminarProducto(${item.id})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
                <div class="total">
                    TOTAL: <span>Bs ${total.toFixed(2)}</span>
                </div>
                <div class="metodo-pago">
                    <label><i class="fas fa-credit-card"></i> Método de Pago</label>
                    <select id="metodoPago" onchange="mostrarPagoDetalle()">
                        <option value="">-- Seleccionar método de pago --</option>
                        <option value="1">💵 Efectivo</option>
                        <option value="2">🏦 Transferencia Bancaria</option>
                        <option value="3">📱 Código QR</option>
                        <option value="4">💳 Tarjeta de Crédito/Débito</option>
                    </select>
                    
                    <div id="detalleEfectivo" class="pago-detalle">
                        <p><i class="fas fa-check-circle" style="color: #4CAF50;"></i> Pagarás en efectivo al momento de recibir el producto.</p>
                    </div>
                    
                    <div id="detalleTransferencia" class="pago-detalle">
                        <div class="bank-info">
                            <strong><i class="fas fa-university"></i> Datos Bancarios:</strong><br>
                            Banco: Banco Unión<br>
                            N° Cuenta: 123-4567890-01<br>
                            Titular: Pet Spa S.R.L.<br>
                            CI/NIT: 123456789
                        </div>
                        <input type="text" id="cuentaTransferencia" placeholder="Número de cuenta del cliente *" value="12345678901234">
                        <small class="dato-ejemplo">📝 Ejemplo: 12345678901234</small>
                    </div>
                    
                    <div id="detalleQR" class="pago-detalle">
                        <div class="qr-code">
                            <p>Escanea el código QR con tu aplicación bancaria:</p>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=Transferencia%20Pet%20Spa%20-%20Monto%20a%20pagar: Bs ${total}" alt="Código QR">
                            <p style="margin-top: 10px; font-size: 12px; color: #64748b;">Monto a pagar: <strong style="color: #4CAF50;">Bs ${total.toFixed(2)}</strong></p>
                        </div>
                    </div>
                    
                    <div id="detalleTarjeta" class="pago-detalle">
                        <input type="text" id="numeroTarjeta" placeholder="Número de tarjeta (16 dígitos)" value="4111111111111111">
                        <small class="dato-ejemplo">📝 Ejemplo: 4111 1111 1111 1111</small>
                        <div style="display: flex; gap: 10px;">
                            <div style="flex: 1;">
                                <input type="text" id="fechaVencimiento" placeholder="MM/AA" value="12/28">
                                <small class="dato-ejemplo">📝 Ejemplo: 12/28</small>
                            </div>
                            <div style="flex: 1;">
                                <input type="text" id="cvv" placeholder="CVV" value="123">
                                <small class="dato-ejemplo">📝 Ejemplo: 123</small>
                            </div>
                        </div>
                        <input type="text" id="nombreTarjeta" placeholder="Nombre del titular" value="Cliente Test">
                        <small class="dato-ejemplo">📝 Ejemplo: Juan Pérez</small>
                    </div>
                </div>
                <div class="acciones">
    <button class="btn btn-danger" onclick="vaciarCarrito()">
        <i class="fas fa-trash-alt"></i> Vaciar Carrito
    </button>
    <button class="btn btn-success" onclick="comprarAhora()">
        <i class="fas fa-credit-card"></i> Confirmar Compra
    </button>
    <button class="btn btn-whatsapp" onclick="enviarPedidoWhatsApp()">
        <i class="fab fa-whatsapp"></i> Enviar pedido por WhatsApp
    </button>
</div>
            `;
            
            container.innerHTML = html;
        }
        function enviarPedidoWhatsApp() {
    const carrito = getCarrito();
    
    if (carrito.length === 0) {
        alert('❌ No hay productos en el carrito');
        return;
    }
    
    // Obtener datos del cliente (desde el DOM o variables)
    let clienteNombre = '';
    let clienteTelefono = '';
    
    // Si tienes datos del cliente en la página, puedes obtenerlos
    if (document.getElementById('userName')) {
        clienteNombre = document.getElementById('userName')?.innerText || '';
    }
    
    let mensaje = "🛍️ *NUEVO PEDIDO - PET SPA*\n\n";
    mensaje += "📅 Fecha: " + new Date().toLocaleString() + "\n";
    mensaje += "━".repeat(30) + "\n\n";
    mensaje += "*PRODUCTOS:*\n";
    
    let total = 0;
    
    carrito.forEach(item => {
        const subtotal = item.precio * item.cantidad;
        mensaje += `✓ ${item.nombre} x${item.cantidad} = Bs ${subtotal.toFixed(2)}\n`;
        total += subtotal;
    });
    
    mensaje += "\n" + "━".repeat(30) + "\n";
    mensaje += `💰 *TOTAL: Bs ${total.toFixed(2)}*\n\n`;
    mensaje += "📦 *Método de pago:* " + getMetodoPagoTexto() + "\n";
    mensaje += "🚚 *Recoger en tienda*\n\n";
    mensaje += "📍 Dirección: Calle Principal #123, Ciudad\n";
    mensaje += "🕐 Horario: Lun-Sáb 9:00-18:00\n";
    mensaje += "📞 Contacto: (591) 2-123456\n\n";
    mensaje += "¡Gracias por tu compra! 🐾";
    
    // NÚMERO DE WHATSAPP DEL NEGOCIO (cambia esto)
    let numeroWhatsapp = "59168101911"; // ← PON AQUÍ TU NÚMERO
    
    let url = "https://wa.me/" + numeroWhatsapp + "?text=" + encodeURIComponent(mensaje);
    window.open(url, '_blank');
}

function getMetodoPagoTexto() {
    const metodoSelect = document.getElementById('metodoPago');
    if (!metodoSelect) return 'Por definir';
    
    const metodo = metodoSelect.value;
    switch(metodo) {
        case '1': return '💵 Efectivo';
        case '2': return '🏦 Transferencia Bancaria';
        case '3': return '📱 Código QR';
        case '4': return '💳 Tarjeta';
        default: return 'Por definir';
    }
}


        renderizarCarrito();

    </script>
</body>
</html>