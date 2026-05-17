<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;
    
    protected $fillable = [
        'id_venta',
        'id_metodo_pago',
        'monto',
        'fecha_pago',
        'estado',
        'referencia'
    ];
    
    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2'
    ];
    
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }
    
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago');
    }
}