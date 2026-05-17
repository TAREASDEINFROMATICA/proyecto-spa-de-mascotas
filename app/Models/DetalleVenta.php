<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';
    protected $primaryKey = 'id_detalle_venta';
    public $timestamps = false;
    
    protected $fillable = [
        'id_venta',
        'id_producto_venta',
        'id_servicio',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];
    
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }
    
    public function producto()
    {
        return $this->belongsTo(ProductoVenta::class, 'id_producto_venta');
    }
    
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }
}