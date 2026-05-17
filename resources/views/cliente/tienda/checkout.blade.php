<!DOCTYPE html>
<html>
<head>
    <title>Finalizar Compra - Pet Spa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 24px; }
        .content { padding: 30px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .card { background: #f8f9fa; border-radius: 16px; padding: 20px; margin-bottom: 20px; }
        .card h3 { margin-bottom: 15px; color: #333; display: flex; align-items: center; gap: 10px; }
        .producto-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .total-checkout { text-align: right; font-size: 18px; margin-top: 15px; padding-top: 15px; border-top: 2px solid #e0e0e0; }
        .total-checkout span { font-size: 24px; color: #4CAF50; font-weight: 700; }
        select, input { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; margin-top: 5px; }
        select:focus, input:focus { outline: none; border-color: #4CAF50; }
        .btn-pagar { width: 100%; background: #4CAF50; color: white; padding: 15px; border: none; border-radius: 12px; font-size: 18px; font-weight: 600; cursor: pointer; margin-top: 20px; transition: all 0.3s; }
        .btn-pagar:hover { background: #45a049; transform: translateY(-2px); }
        .btn-pagar:disabled { background: #ccc; cursor: not-allowed; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 20px; padding: 30px; text-align: center; max-width: 400px; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-credit-card"></i> Finalizar Compra</h1>
        </div>
        
        <div class="content">
            <div id="checkoutContainer">
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 40px; color: #4CAF50;"></i>
                    <p>Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <div id="modalExito" class="modal">
        <div class="modal-content">
            <i class="fas fa-check-circle" style="font-size: 64px; color: #4CAF50; margin-bottom: 20px;"></i>
            <h3>¡Compra Exitosa!</h3>
            <p id="mensajeExito"></p>
            <button onclick="cerrarModal()" class="btn-pagar" style="margin-top: 20px;">Ver Mis Compras</button>
        </div>
    </div>

    <script>
        const token = '{{ $token }}';
        
        function getCarrito() {
            const carrito = localStorage.getItem('carrito');
            return carrito ? JSON.parse(carrito) : [];
        }
        
        function renderizarCheckout() {
            const carrito = getCarrito();
            const container = document.getElementById('checkoutContainer');
            
            if (carrito.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 60px;">
                        <i class="fas fa-shopping-cart" style="font-size: 64px; color: #ccc;"></i>
                        <h3>Tu carrito está vacío</h3>
                        <a href="/cliente/tienda?token=${token}" class="btn-pagar" style="display: inline-block; width: auto; padding: 12px 24px;">
                            Ir a la tienda
                        </a>
                    </div>
                `;
                return;
            }
            
            let total = 0;
            let productosHtml = '';
            
            carrito.forEach(item => {
                const subtotal = item.precio * item.cantidad;
                total += subtotal;
                productosHtml += `
                    <div class="producto-item">
                        <span>${item.nombre} x ${item.cantidad}</span>
                        <span style="font-weight: 600;">Bs ${subtotal.toFixed(2)}</span>
                    </div>
                `;
            });
            
            let metodosHtml = '<option value="">-- Seleccionar --</option>';
            metodosPago.forEach(m => {
                metodosHtml += `<option value="${m.id_metodo_pago}">${m.nombre}</option>`;
            });
            
            container.innerHTML = `
                <div class="grid-2">
                    <div>
                        <div class="card">
                            <h3><i class="fas fa-box"></i> Resumen del Pedido</h3>
                            ${productosHtml}
                            <div class="total-checkout">
                                Total a pagar: <span>Bs ${total.toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="card">
                            <h3><i class="fas fa-credit-card"></i> Método de Pago</h3>
                            <label>Selecciona cómo quieres pagar:</label>
                            <select id="metodoPago">
                                ${metodosHtml}
                            </select>
                            <button class="btn-pagar" onclick="procesarPago()">
                                <i class="fas fa-check-circle"></i> Confirmar Compra
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function procesarPago() {
            const metodoPago = document.getElementById('metodoPago').value;
            const carrito = getCarrito();
            
            if (!metodoPago) {
                alert('❌ Selecciona un método de pago');
                return;
            }
            
            if (carrito.length === 0) {
                alert('❌ El carrito está vacío');
                return;
            }
            
            const productos = carrito.map(item => ({
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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id_metodo_pago: metodoPago,
                    productos: productos
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem('carrito');
                    document.getElementById('mensajeExito').innerHTML = `Tu compra #${data.venta_id} se ha registrado exitosamente.`;
                    document.getElementById('modalExito').style.display = 'flex';
                } else {
                    alert('❌ Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar Compra';
                }
            })
            .catch(error => {
                alert('❌ Error al procesar el pago');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar Compra';
            });
        }
        
        function cerrarModal() {
            document.getElementById('modalExito').style.display = 'none';
            window.location.href = '/cliente/mis-compras?token=' + token;
        }
        
        renderizarCheckout();
    </script>
</body>
</html>