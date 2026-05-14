-- =========================================================
-- BASE DE DATOS: SPA DE MASCOTAS
-- POSTGRESQL - SQL COMPLETO ACTUALIZADO
-- Incluye: duración, temperamento, temperatura,
-- inventario para ventas e inventario para tratamientos
-- =========================================================


-- =========================================================
-- 1. USUARIOS Y ACCESOS
-- =========================================================
create database spa_de_mascotas;

create table roles (
    id_rol serial primary key,
    nombre varchar(30) not null unique,
    descripcion varchar(150)
);

create table usuarios (
    id_usuario serial primary key,
    id_rol integer not null,
    nombres varchar(80) not null,
    apellidos varchar(80) not null,
    correo varchar(120) not null unique,
    contrasena_hash varchar(255) not null,
    telefono varchar(20),
    estado varchar(20) not null default 'activo',
    fecha_registro timestamp not null default current_timestamp,
    constraint fk_usuarios_roles
        foreign key (id_rol) references roles(id_rol),
    constraint chk_usuario_estado
        check (estado in ('activo','inactivo','bloqueado'))
);

-- =========================================================
-- 2. CLIENTES, EMPLEADOS Y MASCOTAS
-- =========================================================

create table clientes (
    id_cliente serial primary key,
    id_usuario integer not null unique,
    direccion varchar(200),
    constraint fk_clientes_usuarios
        foreign key (id_usuario) references usuarios(id_usuario)
);

create table empleados (
    id_empleado serial primary key,
    id_usuario integer not null unique,
    cargo varchar(50) not null,
    especialidad varchar(80),
    capacidad_simultanea integer default 1,
    fecha_ingreso date,

    constraint fk_empleados_usuarios
        foreign key (id_usuario) references usuarios(id_usuario),

    constraint chk_capacidad_simultanea
        check (capacidad_simultanea > 0)
);

create table mascotas (
    id_mascota serial primary key,
    id_cliente integer not null,
    nombre varchar(80) not null,
    especie varchar(40) not null,
    raza varchar(60),
    sexo varchar(15),
    fecha_nacimiento date,
    peso numeric(6,2),
    color varchar(50),
    temperamento_general varchar(30),
    alergias text,
    cuidados_especiales text,
    observaciones text,
    foto varchar(255),
    estado varchar(20) not null default 'activa',
   constraint fk_mascotas_clientes
    foreign key (id_cliente) references clientes(id_cliente)
    on delete cascade,
    constraint chk_mascotas_estado
        check (estado in ('activa','inactiva')),
    constraint chk_mascotas_temperamento
        check (temperamento_general in ('tranquilo','nervioso','agresivo','miedoso','jugueton','otro') or temperamento_general is null)
);

-- =========================================================
-- 3. SERVICIOS, DISPONIBILIDAD Y CITAS
-- =========================================================

create table servicios (
    id_servicio serial primary key,
    nombre varchar(100) not null,
    descripcion text,
    duracion_minutos integer not null,
    precio numeric(10,2) not null,
    tipo_mascota varchar(40),
    estado varchar(20) not null default 'activo',
    constraint chk_servicios_estado
        check (estado in ('activo','inactivo')),
    constraint chk_servicios_duracion
        check (duracion_minutos > 0),
    constraint chk_servicios_precio
        check (precio >= 0)
);

create table disponibilidad_empleado (
    id_disponibilidad serial primary key,
    id_empleado integer not null,
    dia_semana varchar(15) not null,
    hora_inicio time not null,
    hora_fin time not null,
    estado varchar(20) not null default 'disponible',
    constraint fk_disponibilidad_empleado
        foreign key (id_empleado) references empleados(id_empleado),
    constraint chk_disponibilidad_estado
        check (estado in ('disponible','bloqueado')),
    constraint chk_disponibilidad_horas
        check (hora_fin > hora_inicio)
);

create table citas (
    id_cita serial primary key,
    id_mascota integer not null,
    id_servicio integer not null,
    id_empleado integer,
    fecha date not null,
    hora_inicio time not null,
    hora_fin time not null,
    duracion_estimada_minutos integer,
    duracion_real_minutos integer,
    tiempo_estimado_llegada_minutos integer,
    estado varchar(20) not null default 'reservado',
    observaciones text,
    tipo_cita varchar(30) not null default 'normal',
    fecha_registro timestamp not null default current_timestamp,
    constraint fk_citas_mascotas
        foreign key (id_mascota) references mascotas(id_mascota) on delete cascade,
    constraint fk_citas_servicios
        foreign key (id_servicio) references servicios(id_servicio),
    constraint fk_citas_empleados
        foreign key (id_empleado) references empleados(id_empleado) on delete set null ,
    constraint chk_citas_estado
        check (estado in ('reservado','programado','concluido','cancelado')),
    constraint chk_citas_tipo
        check (tipo_cita in ('normal','especial','personalizada')),
    constraint chk_citas_horas
        check (hora_fin > hora_inicio),
    constraint chk_citas_duracion_estimada
        check (duracion_estimada_minutos is null or duracion_estimada_minutos > 0),
    constraint chk_citas_duracion_real
        check (duracion_real_minutos is null or duracion_real_minutos > 0),
    constraint chk_citas_tiempo_llegada
        check (tiempo_estimado_llegada_minutos is null or tiempo_estimado_llegada_minutos >= 0)
);

-- =========================================================
-- 4. FICHA TÉCNICA DEL SERVICIO
-- =========================================================

create table ficha_tecnica (
    id_ficha serial primary key,
    id_cita integer not null unique,
    estado_ingreso text,
    temperamento_observado varchar(30),
    temperatura_corporal numeric(4,2),
    observacion_temperamento text,
    recomendaciones text,
    detalles_servicio text,
    fecha_apertura timestamp not null default current_timestamp,
    fecha_cierre timestamp,
    constraint fk_ficha_citas
        foreign key (id_cita) references citas(id_cita),
    constraint chk_ficha_temperamento
        check (temperamento_observado in ('tranquilo','nervioso','agresivo','miedoso','jugueton','otro') or temperamento_observado is null),
    constraint chk_ficha_temperatura
        check (temperatura_corporal is null or temperatura_corporal between 30.00 and 45.00)
);

create table calificaciones (
    id_calificacion serial primary key,
    id_cita integer not null unique,
    puntuacion integer not null,
    comentario text,
    fecha_calificacion timestamp not null default current_timestamp,
    constraint fk_calificaciones_citas
        foreign key (id_cita) references citas(id_cita),
    constraint chk_puntuacion
        check (puntuacion between 1 and 5)
);

-- =========================================================
-- 5. PROMOCIONES
-- =========================================================

create table promociones (
    id_promocion serial primary key,
    nombre varchar(100) not null,
    descripcion text,
    porcentaje_descuento numeric(5,2) not null,
    fecha_inicio date not null,
    fecha_fin date not null,
    estado varchar(20) not null default 'activa',
    constraint chk_promocion_estado
        check (estado in ('activa','inactiva')),
    constraint chk_promocion_descuento
        check (porcentaje_descuento >= 0 and porcentaje_descuento <= 100),
    constraint chk_promocion_fechas
        check (fecha_fin >= fecha_inicio)
);

create table promocion_servicios (
    id_promocion integer not null,
    id_servicio integer not null,
    primary key (id_promocion, id_servicio),
    constraint fk_prom_serv_prom
        foreign key (id_promocion) references promociones(id_promocion),
    constraint fk_prom_serv_serv
        foreign key (id_servicio) references servicios(id_servicio)
);

-- =========================================================
-- CATEGORÍAS DE PRODUCTOS
-- =========================================================

create table categorias_producto (
    id_categoria serial primary key,
    nombre varchar(80) not null unique,
    descripcion varchar(150)
);

-- =========================================================
-- PRODUCTOS PARA VENTA
-- =========================================================

create table productos_venta (
    id_producto_venta serial primary key,
    id_categoria integer not null,
    nombre varchar(100) not null,
    descripcion text,
    precio_compra numeric(10,2),
    precio_venta numeric(10,2) not null,
    stock integer not null default 0,
    stock_minimo integer not null default 0,
    unidad_medida varchar(20),
    estado varchar(20) not null default 'activo',

    constraint fk_productos_venta_categorias
        foreign key (id_categoria) references categorias_producto(id_categoria),

    constraint chk_productos_venta_estado
        check (estado in ('activo','inactivo')),

    constraint chk_productos_venta_precios
        check ((precio_compra is null or precio_compra >= 0) and precio_venta >= 0),

    constraint chk_productos_venta_stock
        check (stock >= 0 and stock_minimo >= 0)
);

-- =========================================================
-- MOVIMIENTOS DE PRODUCTOS PARA VENTA
-- =========================================================

create table movimientos_productos_venta (
    id_movimiento serial primary key,
    id_producto_venta integer not null,
    tipo_movimiento varchar(10) not null,
    cantidad integer not null,
    fecha_movimiento timestamp not null default current_timestamp,
    motivo varchar(100),
    referencia varchar(100),

    constraint fk_mov_productos_venta
        foreign key (id_producto_venta) references productos_venta(id_producto_venta),

    constraint chk_mov_productos_venta_tipo
        check (tipo_movimiento in ('ingreso','salida')),

    constraint chk_mov_productos_venta_cantidad
        check (cantidad > 0)
);

-- =========================================================
-- INSUMOS PARA TRATAMIENTO
-- =========================================================

create table insumos_tratamiento (
    id_insumo serial primary key,
    nombre varchar(100) not null,
    descripcion text,
    stock numeric(10,2) not null default 0,
    stock_minimo numeric(10,2) not null default 0,
    unidad_medida varchar(20) not null,
    costo_unitario numeric(10,2),
    estado varchar(20) not null default 'activo',

    constraint chk_insumos_estado
        check (estado in ('activo','inactivo')),

    constraint chk_insumos_stock
        check (stock >= 0 and stock_minimo >= 0),

    constraint chk_insumos_costo
        check (costo_unitario is null or costo_unitario >= 0)
);

-- =========================================================
-- MOVIMIENTOS DE INSUMOS DE TRATAMIENTO
-- =========================================================

create table movimientos_insumos_tratamiento (
    id_movimiento serial primary key,
    id_insumo integer not null,
    tipo_movimiento varchar(10) not null,
    cantidad numeric(10,2) not null,
    fecha_movimiento timestamp not null default current_timestamp,
    motivo varchar(100),
    referencia varchar(100),

    constraint fk_mov_insumos_tratamiento
        foreign key (id_insumo) references insumos_tratamiento(id_insumo),

    constraint chk_mov_insumos_tipo
        check (tipo_movimiento in ('ingreso','salida')),

    constraint chk_mov_insumos_cantidad
        check (cantidad > 0)
);

-- =========================================================
-- CONSUMO (MOVIDO AQUÍ, NO CAMBIADO)
-- =========================================================

create table consumo_insumos_cita (
    id_consumo serial primary key,
    id_cita integer not null,
    id_insumo integer not null,
    cantidad_usada numeric(10,2) not null,

    foreign key (id_cita) references citas(id_cita),
    foreign key (id_insumo) references insumos_tratamiento(id_insumo)
);

-- =========================================================
-- 8. PROVEEDORES Y COMPRAS
-- =========================================================

create table proveedores (
    id_proveedor serial primary key,
    nombre varchar(120) not null,
    contacto varchar(100),
    telefono varchar(20),
    correo varchar(120),
    direccion varchar(200),
    estado varchar(20) not null default 'activo',
    constraint chk_proveedores_estado
        check (estado in ('activo','inactivo'))
);

create table compras (
    id_compra serial primary key,
    id_proveedor integer not null,
    fecha_compra date not null,
    total numeric(12,2) not null,
    observaciones text,
    constraint fk_compras_proveedores
        foreign key (id_proveedor) references proveedores(id_proveedor),
    constraint chk_compras_total
        check (total >= 0)
);

create table detalle_compras_productos_venta (
    id_detalle_compra serial primary key,
    id_compra integer not null,
    id_producto_venta integer not null,
    cantidad integer not null,
    precio_unitario numeric(10,2) not null,
    subtotal numeric(12,2) not null,
    constraint fk_det_comp_venta_compra
        foreign key (id_compra) references compras(id_compra),
    constraint fk_det_comp_venta_producto
        foreign key (id_producto_venta) references productos_venta(id_producto_venta),
    constraint chk_det_comp_venta_cantidad
        check (cantidad > 0),
    constraint chk_det_comp_venta_precios
        check (precio_unitario >= 0 and subtotal >= 0)
);

create table detalle_compras_insumos_tratamiento (
    id_detalle_compra serial primary key,
    id_compra integer not null,
    id_insumo integer not null,
    cantidad numeric(10,2) not null,
    precio_unitario numeric(10,2) not null,
    subtotal numeric(12,2) not null,
    constraint fk_det_comp_insumo_compra
        foreign key (id_compra) references compras(id_compra),
    constraint fk_det_comp_insumo_insumo
        foreign key (id_insumo) references insumos_tratamiento(id_insumo),
    constraint chk_det_comp_insumo_cantidad
        check (cantidad > 0),
    constraint chk_det_comp_insumo_precios
        check (precio_unitario >= 0 and subtotal >= 0)
);

-- =========================================================
-- 9. CARRITO Y VENTAS
-- =========================================================

create table carritos (
    id_carrito serial primary key,
    id_cliente integer not null,
    fecha_creacion timestamp not null default current_timestamp,
    estado varchar(20) not null default 'abierto',
    constraint fk_carritos_clientes
        foreign key (id_cliente) references clientes(id_cliente),
    constraint chk_carrito_estado
        check (estado in ('abierto','cerrado','anulado'))
);

create table carrito_detalle (
    id_detalle_carrito serial primary key,
    id_carrito integer not null,
    id_producto_venta integer not null,
    cantidad integer not null,
    precio_unitario numeric(10,2) not null,
    subtotal numeric(12,2) not null,
    constraint fk_carrito_detalle_carrito
        foreign key (id_carrito) references carritos(id_carrito),
    constraint fk_carrito_detalle_producto_venta
        foreign key (id_producto_venta) references productos_venta(id_producto_venta),
    constraint chk_carrito_detalle_cantidad
        check (cantidad > 0),
    constraint chk_carrito_detalle_precios
        check (precio_unitario >= 0 and subtotal >= 0)
);

create table ventas (
    id_venta serial primary key,
    id_cliente integer not null,
    id_cita integer,
    fecha_venta timestamp not null default current_timestamp,
    total numeric(12,2) not null,
    estado varchar(20) not null default 'pendiente',
    constraint fk_ventas_clientes
        foreign key (id_cliente) references clientes(id_cliente) on delete restrict,
    constraint fk_ventas_citas
        foreign key (id_cita) references citas(id_cita),
    constraint chk_ventas_estado
        check (estado in ('pendiente','pagada','anulada')),
    constraint chk_ventas_total
        check (total >= 0)
);

create table detalle_ventas (
    id_detalle_venta serial primary key,
    id_venta integer not null,
    id_producto_venta integer,
    id_servicio integer,
    cantidad integer not null,
    precio_unitario numeric(10,2) not null,
    subtotal numeric(12,2) not null,
    constraint fk_detalle_venta_venta
        foreign key (id_venta) references ventas(id_venta),
    constraint fk_detalle_venta_producto_venta
        foreign key (id_producto_venta) references productos_venta(id_producto_venta),
    constraint fk_detalle_venta_servicio
        foreign key (id_servicio) references servicios(id_servicio),
    constraint chk_detalle_venta_producto_o_servicio
        check (
            (id_producto_venta is not null and id_servicio is null)
            or
            (id_producto_venta is null and id_servicio is not null)
        ),
    constraint chk_detalle_venta_cantidad
        check (cantidad > 0),
    constraint chk_detalle_venta_precios
        check (precio_unitario >= 0 and subtotal >= 0)
);

-- =========================================================
-- 10. PAGOS Y COMPROBANTES
-- =========================================================
create table metodos_pago (
    id_metodo_pago serial primary key,
    nombre varchar(30) not null unique,
    descripcion varchar(100)
);
create table pagos (
    id_pago serial primary key,
    id_venta integer not null,
    id_metodo_pago integer not null,
    monto numeric(12,2) not null,
    fecha_pago timestamp not null default current_timestamp,
    estado varchar(20) not null default 'confirmado',
    referencia varchar(100),
    constraint fk_pagos_ventas
        foreign key (id_venta) references ventas(id_venta),
    constraint fk_pagos_metodos
        foreign key (id_metodo_pago) references metodos_pago(id_metodo_pago),
    constraint chk_pagos_estado
        check (estado in ('confirmado','pendiente','anulado')),
    constraint chk_pagos_monto
        check (monto >= 0)
);

create table comprobantes (
    id_comprobante serial primary key,
    id_venta integer not null unique,
    tipo_comprobante varchar(30) not null,
    numero_comprobante varchar(50) not null unique,
    fecha_emision timestamp not null default current_timestamp,
    archivo varchar(255),
    constraint fk_comprobantes_ventas
        foreign key (id_venta) references ventas(id_venta)
);

-- =========================================================
-- 11. NOTIFICACIONES
-- =========================================================

create table notificaciones (
    id_notificacion serial primary key,
    id_usuario integer not null,
    tipo varchar(30) not null,
    mensaje text not null,
    fecha_envio timestamp not null default current_timestamp,
    estado varchar(20) not null default 'pendiente',
    constraint fk_notificaciones_usuarios
        foreign key (id_usuario) references usuarios(id_usuario),
    constraint chk_notificaciones_estado
        check (estado in ('pendiente','enviada','leida','fallida'))
);

-- =========================================================
-- 🔹 CHECKLIST (FALTABA)
-- =========================================================

create table checklist_items (
    id_item serial primary key,
    nombre varchar(100) not null,
    requiere_observacion boolean default false
);

create table ficha_checklist (
    id_registro serial primary key,
    id_ficha integer not null,
    id_item integer not null,
    realizado boolean default false,
    observacion text,
    unique (id_ficha, id_item),
    foreign key (id_ficha) references ficha_tecnica(id_ficha),
    foreign key (id_item) references checklist_items(id_item)
);

-- =========================================================
-- 🔹 FOTOS ANTES / DESPUÉS (FALTABA)
-- =========================================================

create table fotos_mascota (
    id_foto serial primary key,
    id_ficha integer not null,
    url varchar(255) not null,
    tipo varchar(20) not null, -- antes / despues
    constraint chk_tipo_foto
        check (tipo in ('antes','despues')),
    foreign key (id_ficha) references ficha_tecnica(id_ficha)
);

-- =========================================================
-- 🔹 HISTORIAL DE MASCOTA (FALTABA)
-- =========================================================

create table historial_mascota (
    id_historial serial primary key,
    id_mascota integer not null,
    tipo_evento varchar(50),
    descripcion text,
    fecha timestamp default current_timestamp,

    foreign key (id_mascota) references mascotas(id_mascota)
);

-- =========================================================
-- 🔹 VACUNAS (FALTABA)
-- =========================================================

create table vacunas_mascota (
    id_vacuna serial primary key,
    id_mascota integer not null,
    nombre varchar(100),
    fecha_aplicacion date,
    fecha_vencimiento date,

    foreign key (id_mascota) references mascotas(id_mascota),
    constraint chk_vacunas_fechas
        check (fecha_vencimiento is null or fecha_vencimiento >= fecha_aplicacion)
);

-- =========================================================
-- 🔹 VARIANTES DE PRODUCTOS (FALTABA)
-- =========================================================

create table producto_variantes (
    id_variante serial primary key,
    id_producto_venta integer not null,
    atributo varchar(50),
    valor varchar(50),
    precio_extra numeric(10,2) default 0,
    stock integer default 0,
    sku varchar(100) unique,

    constraint chk_variante_stock check (stock >= 0),

    foreign key (id_producto_venta)
        references productos_venta(id_producto_venta)
);

-- =========================================================
-- 🔹 BLOQUEOS DE AGENDA (FALTABA)
-- =========================================================

create table bloqueos_agenda (
    id_bloqueo serial primary key,
    id_empleado integer,
    tipo varchar(30), -- feriado, mantenimiento, ausencia
    fecha_inicio timestamp not null,
    fecha_expiracion timestamp,
    motivo text,
    constraint chk_bloqueo_fechas
        check (fecha_expiracion is null or fecha_expiracion > fecha_inicio),
    foreign key (id_empleado) references empleados(id_empleado)
);

-- =========================================================
-- 🔹 SESIONES (JWT) (FALTABA)
-- =========================================================

create table sesiones_usuario (
    id_sesion serial primary key,
    id_usuario integer not null,
    refresh_token text not null unique,
    fecha_creacion timestamp default current_timestamp,
    fecha_expiracion timestamp,
    ip_address varchar(50),
    user_agent varchar(200),
    estado varchar(20) default 'activa',

    foreign key (id_usuario) references usuarios(id_usuario) ON DELETE CASCADE,

    constraint chk_sesion_estado
        check (estado in ('activa','revocada','expirada')) 
);
-- =========================================================
-- 🔹 LOG DE NOTIFICACIONES (PRO)
-- =========================================================

create table log_notificaciones (
    id_log serial primary key,
    id_notificacion integer,
    estado varchar(20),
    intento integer,
    fecha timestamp default current_timestamp,

    foreign key (id_notificacion) references notificaciones(id_notificacion)
);

create table servicio_checklist (
    id_servicio integer not null,
    id_item integer not null,
    primary key (id_servicio, id_item),
    foreign key (id_servicio) references servicios(id_servicio),
    foreign key (id_item) references checklist_items(id_item)
);
create table log_sistema (
    id_log serial primary key,
    id_usuario integer,
    accion varchar(100),
    fecha timestamp default current_timestamp,
    ip_address varchar(50),
    user_agent varchar(200),

    foreign key (id_usuario) references usuarios(id_usuario)
);

