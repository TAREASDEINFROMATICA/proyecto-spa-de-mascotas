<?php
// app/Models/ConsumoInsumoCita.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumoInsumoCita extends Model
{
    protected $table = 'consumo_insumos_cita';
    protected $primaryKey = 'id_consumo';
    public $timestamps = false;
    
    protected $fillable = [
        'id_cita',
        'id_insumo',
        'cantidad_usada'
    ];
    
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita');
    }
    
    public function insumo()
    {
        return $this->belongsTo(InsumoTratamiento::class, 'id_insumo');
    }
}