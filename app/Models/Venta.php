<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';
    public $timestamps = false;
    
    protected $fillable = [
        'id_cliente',
        'id_cita',
        'fecha_venta',
        'total',
        'estado'
    ];
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
    
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta');
    }
    
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_venta');
    }
    
    public function comprobante()
    {
        return $this->hasOne(Comprobante::class, 'id_venta');
    }
}