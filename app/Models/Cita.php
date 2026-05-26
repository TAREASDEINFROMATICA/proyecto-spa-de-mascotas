<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';
    protected $primaryKey = 'id_cita';
    public $timestamps = false;

    protected $fillable = [
        'id_mascota',
        'id_servicio',
        'id_empleado',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'duracion_estimada_minutos',
        'duracion_real_minutos',
        'tiempo_estimado_llegada_minutos',
        'estado',
        'observaciones',
        'tipo_cita',
        'fecha_registro'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio' => 'string',
        'hora_fin' => 'string',
        'fecha_registro' => 'datetime'
    ];

    // Relaciones
    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
    // En app/Models/Cita.php
public function fichaTecnica()
{
    return $this->hasOne(FichaTecnica::class, 'id_cita');
}
public function calificacion()
{
    return $this->hasOne(Calificacion::class, 'id_cita');
}
// Relación con ficha_checklist
public function fichaChecklist()
{
    return $this->hasMany(FichaChecklist::class, 'id_cita', 'id_cita');
}
}