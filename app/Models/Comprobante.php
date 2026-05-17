<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    protected $table = 'comprobantes';
    protected $primaryKey = 'id_comprobante';
    public $timestamps = false;
    
    protected $fillable = [
        'id_venta',
        'tipo_comprobante',
        'numero_comprobante',
        'fecha_emision',
        'archivo'
    ];
    
    protected $casts = [
        'fecha_emision' => 'datetime'
    ];
    
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }
}