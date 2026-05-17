<!DOCTYPE html>
<html>
<head>
    <title>Mi Carrito - Pet Spa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-primary { background: white; color: #ff9800; }
        .btn-primary:hover { transform: translateY(-2px); }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .btn-success { background: #4CAF50; color: white; border: none; cursor: pointer; }
        .btn-success:hover { background: #45a049; transform: translateY(-2px); }
        .btn-danger { background: #f44336; color: white; border: none; cursor: pointer; }
        .btn-danger:hover { background: #d32f2f; }
        .content { padding: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .producto-nombre { font-weight: 500; }
        .producto-precio { color: #4CAF50; font-weight: 600; }
        .cantidad-input { width: 60px; padding: 8px; border: 1px solid #ddd; border-radius: 8px; text-align: center; }
        .total { text-align: right; font-size: 20px; margin-top: 20px; padding-top: 20px; border-top: 2px solid #e0e0e0; }
        .total span { font-size: 28px; color: #4CAF50; font-weight: 700; }
        .empty-cart { text-align: center; padding: 60px; color: #999; }
        .empty-cart i { font-size: 64px; margin-bottom: 20px; display: block; }
        .acciones { display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px; }
        @media (max-width: 768px) { .header { flex-direction: column; text-align: center; } table { font-size: 12px; } .cantidad-input { width: 50px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> Mi Carrito</h1>
            <a href="/cliente/tienda?token={{ $token }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Seguir Comprando
            </a>
        </div>
        
        <div class="content">
            <div id="carritoContainer">
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 40px; color: #4CAF50;"></i>
                    <p>Cargando carrito...</p>
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
            actualizarBadge();
        }
        
        function actualizarBadge() {
            const carrito = getCarrito();
            const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
            const badge = document.getElementById('cartCount');
            if (badge) badge.textContent = totalItems;
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
        
        function renderizarCarrito() {
            const carrito = getCarrito();
            const container = document.getElementById('carritoContainer');
            
            if (carrito.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Tu carrito está vacío</h3>
                        <p>¡Agrega productos para comenzar tu compra!</p>
                        <a href="/cliente/tienda?token=${token}" class="btn btn-primary" style="margin-top: 20px;">
                            <i class="fas fa-store"></i> Ir a la tienda
                        </a>
                    </div>
                `;
                return;
            }
            
            let total = 0;
            let html = `
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
                        <td class="producto-nombre">${item.nombre}</td>
                        <td class="producto-precio">Bs ${item.precio.toFixed(2)}</td>
                        <td>
                            <input type="number" class="cantidad-input" value="${item.cantidad}" min="1" max="${item.stock}" 
                                   onchange="actualizarCantidad(${item.id}, parseInt(this.value))">
                        </td>
                        <td class="producto-precio">Bs ${subtotal.toFixed(2)}</td>
                        <td>
                            <button class="btn-danger" style="padding: 5px 10px; border-radius: 8px;" onclick="eliminarProducto(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                    </tbody>
                </table>
                <div class="total">
                    TOTAL: <span>Bs ${total.toFixed(2)}</span>
                </div>
                <div class="acciones">
                    <button class="btn-danger" onclick="vaciarCarrito()">
                        <i class="fas fa-trash-alt"></i> Vaciar Carrito
                    </button>
                    <a href="/cliente/checkout?token=${token}" class="btn-success" style="padding: 12px 24px; text-decoration: none; border-radius: 12px;">
                        <i class="fas fa-credit-card"></i> Finalizar Compra
                    </a>
                </div>
            `;
            
            container.innerHTML = html;
        }
        
        renderizarCarrito();
    </script>
</body>
</html>