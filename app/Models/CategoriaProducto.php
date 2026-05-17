<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $table = 'categorias_producto';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];
    
    public function productos()
    {
        return $this->hasMany(ProductoVenta::class, 'id_categoria');
    }
}