<?php
// app/Models/InsumoTratamiento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsumoTratamiento extends Model
{
    protected $table = 'insumos_tratamiento';
    protected $primaryKey = 'id_insumo';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'stock',
        'stock_minimo',
        'unidad_medida',
        'costo_unitario',
        'estado'
    ];
    
    public function consumos()
    {
        return $this->hasMany(ConsumoInsumoCita::class, 'id_insumo');
    }
}