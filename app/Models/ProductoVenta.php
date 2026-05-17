<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoVenta extends Model
{
    protected $table = 'productos_venta';
    protected $primaryKey = 'id_producto_venta';
    public $timestamps = false;
    
    protected $fillable = [
        'id_categoria',
        'nombre',
        'descripcion',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'unidad_medida',
        'estado'
    ];
    
    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'id_categoria');
    }
}