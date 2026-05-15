<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloqueoAgenda extends Model
{
    protected $table = 'bloqueos_agenda';
    protected $primaryKey = 'id_bloqueo';
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'tipo',
        'fecha_inicio',
        'fecha_expiracion',
        'motivo'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_expiracion' => 'datetime',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
}