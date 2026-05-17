<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';
    protected $primaryKey = 'id_calificacion';
    public $timestamps = false;
    
    protected $fillable = [
        'id_cita',
        'puntuacion',
        'comentario',
        'fecha_calificacion'
    ];
    
    protected $casts = [
        'puntuacion' => 'integer',
        'fecha_calificacion' => 'datetime'
    ];
    
    // Relación con la cita
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita');
    }
    
    // Relación con la mascota a través de la cita
    public function mascota()
    {
        return $this->hasOneThrough(Mascota::class, Cita::class, 'id_cita', 'id_mascota', 'id_cita', 'id_mascota');
    }
    
    // Relación con el servicio a través de la cita
    public function servicio()
    {
        return $this->hasOneThrough(Servicio::class, Cita::class, 'id_cita', 'id_servicio', 'id_cita', 'id_servicio');
    }
}