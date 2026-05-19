<!DOCTYPE html>
<html>
<head>
    <title>Tienda - Pet Spa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 24px; }
        .header-buttons { display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; border: none; cursor: pointer; }
        .btn-primary { background: white; color: #4CAF50; }
        .btn-primary:hover { transform: translateY(-2px); }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .btn-carrito { background: #ff9800; color: white; position: relative; }
        .btn-carrito:hover { background: #f57c00; }
        .btn-detalle { background: #2196F3; color: white; margin-top: 8px; width: 100%; padding: 8px; border-radius: 10px; font-size: 13px; }
        .btn-detalle:hover { background: #1976D2; }
        .cart-badge { position: absolute; top: -8px; right: -8px; background: #f44336; color: white; border-radius: 50%; padding: 4px 8px; font-size: 10px; font-weight: bold; }
        .content { padding: 30px; }
        .productos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; margin-top: 20px; }
        .producto-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s; border: 1px solid #eee; }
        .producto-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .producto-imagen { height: 200px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .producto-imagen img { width: 100%; height: 100%; object-fit: cover; }
        .producto-imagen i { font-size: 60px; color: #ccc; }
        .producto-info { padding: 20px; }
        .producto-nombre { font-size: 18px; font-weight: 700; color: #333; margin-bottom: 8px; }
        .producto-descripcion { font-size: 13px; color: #666; margin-bottom: 12px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .producto-precio { font-size: 22px; font-weight: 700; color: #4CAF50; margin: 10px 0; }
        .producto-stock { font-size: 12px; color: #666; margin-bottom: 15px; }
        .producto-stock.bajo { color: #f44336; }
        .btn-agregar { width: 100%; background: #4CAF50; color: white; padding: 12px; border: none; border-radius: 12px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s; }
        .btn-agregar:hover { background: #45a049; transform: scale(1.02); }
        .btn-agregar:disabled { background: #ccc; cursor: not-allowed; }
        .pagination { margin-top: 30px; display: flex; justify-content: center; }
        .toast { position: fixed; bottom: 20px; right: 20px; background: #4CAF50; color: white; padding: 12px 24px; border-radius: 12px; display: none; align-items: center; gap: 10px; z-index: 1000; animation: slideIn 0.3s ease; }
        
        /* Modal de detalle */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 24px; max-width: 500px; width: 90%; max-height: 90%; overflow-y: auto; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .modal-header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { margin: 0; }
        .close-modal { background: none; border: none; color: white; font-size: 28px; cursor: pointer; }
        .modal-body { padding: 20px; }
        .modal-imagen { text-align: center; margin-bottom: 20px; }
        .modal-imagen img { max-width: 200px; border-radius: 12px; }
        .modal-info { margin-bottom: 15px; }
        .modal-info label { font-weight: 600; color: #333; width: 100px; display: inline-block; }
        .modal-info p { display: inline; color: #666; }
        .modal-precio { font-size: 24px; color: #4CAF50; font-weight: 700; margin: 15px 0; text-align: center; }
        .btn-modal-agregar { background: #4CAF50; color: white; border: none; padding: 12px; border-radius: 12px; width: 100%; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; }
        
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @media (max-width: 768px) { .header { flex-direction: column; text-align: center; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-store"></i> Tienda Pet Spa</h1>
            <div class="header-buttons">
                <a href="#" id="enlaceCarrito" class="btn btn-carrito">
                    <i class="fas fa-shopping-cart"></i> Mi Carrito
                    <span id="cartCount" class="cart-badge">0</span>
                </a>
                <a href="/cliente/dashboard?token={{ $token }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="content">
            <h2><i class="fas fa-paw"></i> Productos Disponibles</h2>
            
            <div class="productos-grid" id="productosGrid">
                @foreach($productos as $producto)
                <div class="producto-card" 
                     data-id="{{ $producto->id_producto_venta }}" 
                     data-nombre="{{ $producto->nombre }}" 
                     data-descripcion="{{ $producto->descripcion ?? 'Sin descripción' }}" 
                     data-precio="{{ $producto->precio_venta }}" 
                     data-stock="{{ $producto->stock }}"
                     data-imagen="{{ $producto->imagen ? Storage::url($producto->imagen) : '' }}">
                    <div class="producto-imagen">
                        @if($producto->imagen)
                            <img src="{{ Storage::url($producto->imagen) }}" alt="{{ $producto->nombre }}">
                        @else
                            <i class="fas fa-box"></i>
                        @endif
                    </div>
                    <div class="producto-info">
                        <div class="producto-nombre">{{ $producto->nombre }}</div>
                        @if($producto->descripcion)
                            <div class="producto-descripcion">{{ Str::limit($producto->descripcion, 80) }}</div>
                        @endif
                        <div class="producto-precio">Bs {{ number_format($producto->precio_venta, 2) }}</div>
                        <div class="producto-stock {{ $producto->stock <= 5 ? 'bajo' : '' }}">
                            <i class="fas fa-boxes"></i> Stock: {{ $producto->stock }} unidades
                        </div>
                        <button class="btn-agregar" onclick="agregarAlCarrito(this)">
                            <i class="fas fa-cart-plus"></i> Agregar al Carrito
                        </button>
                        <button class="btn-detalle" onclick="verDetalle(this)">
                            <i class="fas fa-info-circle"></i> Ver Detalles
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="pagination">
                {{ $productos->appends(['token' => $token])->links() }}
            </div>
        </div>
    </div>

    <!-- Modal de detalle de producto -->
    <div id="modalDetalle" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitulo">Detalle del Producto</h3>
                <button class="close-modal" onclick="cerrarModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-imagen" id="modalImagen"></div>
                <div class="modal-info">
                    <label>Nombre:</label>
                    <p id="modalNombre"></p>
                </div>
                <div class="modal-info">
                    <label>Descripción:</label>
                    <p id="modalDescripcion"></p>
                </div>
                <div class="modal-info">
                    <label>Stock:</label>
                    <p id="modalStock"></p>
                </div>
                <div class="modal-precio" id="modalPrecio"></div>
                <button class="btn-modal-agregar" onclick="agregarDesdeModal()">
                    <i class="fas fa-cart-plus"></i> Agregar al Carrito
                </button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast">
        <i class="fas fa-check-circle"></i>
        <span>Producto agregado al carrito</span>
    </div>

    <script>
        const token = '{{ $token }}';
        let productoActual = null;
        
        // =========================================================
        // CARRITO CON localStorage
        // =========================================================
        
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
            if (badge) {
                badge.textContent = totalItems;
                badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
            }
        }
        
        function mostrarToast(mensaje) {
            const toast = document.getElementById('toast');
            toast.querySelector('span').textContent = mensaje;
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 2000);
        }
        
        function agregarAlCarrito(btn) {
            const card = btn.closest('.producto-card');
            const id = parseInt(card.dataset.id);
            const nombre = card.dataset.nombre;
            const precio = parseFloat(card.dataset.precio);
            const stock = parseInt(card.dataset.stock);
            
            let carrito = getCarrito();
            const existente = carrito.find(item => item.id === id);
            
            if (existente) {
                if (existente.cantidad + 1 > stock) {
                    mostrarToast('❌ Stock insuficiente');
                    return;
                }
                existente.cantidad++;
            } else {
                carrito.push({
                    id: id,
                    nombre: nombre,
                    precio: precio,
                    cantidad: 1,
                    stock: stock
                });
            }
            
            guardarCarrito(carrito);
            mostrarToast('✅ ' + nombre + ' agregado al carrito');
            
            btn.style.transform = 'scale(0.95)';
            setTimeout(() => { btn.style.transform = ''; }, 200);
        }
        
        function verDetalle(btn) {
            const card = btn.closest('.producto-card');
            productoActual = {
                id: parseInt(card.dataset.id),
                nombre: card.dataset.nombre,
                descripcion: card.dataset.descripcion,
                precio: parseFloat(card.dataset.precio),
                stock: parseInt(card.dataset.stock),
                imagen: card.dataset.imagen
            };
            
            document.getElementById('modalTitulo').innerHTML = productoActual.nombre;
            document.getElementById('modalNombre').innerHTML = productoActual.nombre;
            document.getElementById('modalDescripcion').innerHTML = productoActual.descripcion || 'Sin descripción';
            document.getElementById('modalStock').innerHTML = productoActual.stock + ' unidades';
            document.getElementById('modalPrecio').innerHTML = 'Bs ' + productoActual.precio.toFixed(2);
            
            const modalImagen = document.getElementById('modalImagen');
            if (productoActual.imagen) {
                modalImagen.innerHTML = '<img src="' + productoActual.imagen + '" alt="' + productoActual.nombre + '">';
            } else {
                modalImagen.innerHTML = '<i class="fas fa-box" style="font-size: 80px; color: #ccc;"></i>';
            }
            
            document.getElementById('modalDetalle').style.display = 'flex';
        }
        
        function agregarDesdeModal() {
            if (!productoActual) return;
            
            let carrito = getCarrito();
            const existente = carrito.find(item => item.id === productoActual.id);
            
            if (existente) {
                if (existente.cantidad + 1 > productoActual.stock) {
                    mostrarToast('❌ Stock insuficiente');
                    return;
                }
                existente.cantidad++;
            } else {
                carrito.push({
                    id: productoActual.id,
                    nombre: productoActual.nombre,
                    precio: productoActual.precio,
                    cantidad: 1,
                    stock: productoActual.stock
                });
            }
            
            guardarCarrito(carrito);
            mostrarToast('✅ ' + productoActual.nombre + ' agregado al carrito');
            cerrarModal();
        }
        
        function cerrarModal() {
            document.getElementById('modalDetalle').style.display = 'none';
            productoActual = null;
        }
        
        // Enlace del carrito
        const enlaceCarrito = document.getElementById('enlaceCarrito');
        if (enlaceCarrito) {
            enlaceCarrito.href = '/cliente/carrito?token=' + token;
        }
        
        // Actualizar badge al cargar
        actualizarBadge();
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('modalDetalle');
            if (event.target == modal) {
                cerrarModal();
            }
        }
    </script>
</body>
</html>