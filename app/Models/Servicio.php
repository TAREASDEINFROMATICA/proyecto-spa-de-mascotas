<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion_minutos',
        'precio',
        'tipo_mascota',
        'estado'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'duracion_minutos' => 'integer'
    ];

    // Scope para servicios activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function isActivo()
    {
        return $this->estado === 'activo';
    }

    // Obtener duración con ajuste por tamaño de mascota
    public function getDuracionAjustada($tamaño)
    {
        $duracion = $this->duracion_minutos;
        
        $ajustes = [
            'pequeño' => 1.0,
            'mediano' => 1.10,  // +10%
            'grande' => 1.15,   // +15%
            'gigante' => 1.30,  // +30%
        ];
        
        $factor = $ajustes[strtolower($tamaño)] ?? 1.0;
        
        return round($duracion * $factor);
    }
    public function citas()
{
    return $this->hasMany(Cita::class, 'id_servicio');
}
}