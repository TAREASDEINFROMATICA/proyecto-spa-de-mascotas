<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial; padding: 50px; background: #f5f5f5; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        button { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        select, input { width: 100%; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Finalizar Compra</h1>
        
        @php $token = request()->query('token'); @endphp
        
        <div id="carritoInfo"></div>
        
        <label>Método de Pago:</label>
        <select id="metodoPago">
            <option value="">-- Seleccionar --</option>
            <option value="1">💵 Efectivo</option>
            <option value="2">🏦 Transferencia</option>
            <option value="3">📱 QR</option>
        </select>
        
        <button onclick="procesarPago()">Confirmar Compra</button>
        
        <br><br>
        <a href="/cliente/carrito?token={{ $token }}">← Volver al Carrito</a>
    </div>
    
    <script>
        const token = '{{ $token }}';
        
        function getCarrito() {
            const carrito = localStorage.getItem('carrito');
            console.log('Carrito desde localStorage:', carrito);
            return carrito ? JSON.parse(carrito) : [];
        }
        
        function mostrarCarrito() {
            const carrito = getCarrito();
            let html = '<h3>Productos:</h3><ul>';
            let total = 0;
            carrito.forEach(item => {
                html += `<li>${item.nombre} x ${item.cantidad} = Bs ${item.precio * item.cantidad}</li>`;
                total += item.precio * item.cantidad;
            });
            html += `</ul><h3>Total: Bs ${total}</h3>`;
            document.getElementById('carritoInfo').innerHTML = html;
        }
        
        function procesarPago() {
            const metodoPago = document.getElementById('metodoPago').value;
            const carrito = getCarrito();
            
            console.log('Método de pago:', metodoPago);
            console.log('Carrito a procesar:', carrito);
            
            if (!metodoPago) {
                alert('Selecciona método de pago');
                return;
            }
            
            if (carrito.length === 0) {
                alert('Carrito vacío');
                return;
            }
            
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = 'Procesando...';
            
            fetch('/cliente/procesar-pago?token=' + token, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ id_metodo_pago: metodoPago })
            })
            .then(res => res.json())
            .then(data => {
                console.log('Respuesta del servidor:', data);
                if (data.success) {
                    localStorage.removeItem('carrito');
                    alert('✅ Compra exitosa');
                    window.location.href = '/cliente/mis-compras?token=' + token;
                } else {
                    alert('❌ Error: ' + data.message);
                    btn.disabled = false;
                    btn.textContent = 'Confirmar Compra';
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                alert('❌ Error de conexión: ' + error.message);
                btn.disabled = false;
                btn.textContent = 'Confirmar Compra';
            });
        }
        
        mostrarCarrito();
    </script>
</body>
</html>