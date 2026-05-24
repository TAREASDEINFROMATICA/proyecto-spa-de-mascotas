<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiaNoLaborable extends Model
{
    use HasFactory;

    protected $table = 'dias_no_laborables';
    protected $primaryKey = 'id_dia_no_laborable';

    protected $fillable = [
        'fecha',
        'tipo',
        'motivo',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'string'
    ];

    // Tipos disponibles
    public static function tipos()
    {
        return [
            'feriado' => '🎉 Feriado Nacional',
            'mantenimiento' => '🔧 Mantenimiento',
            'ausencia' => '👤 Ausencia de Personal',
            'descanso' => '😴 Descanso Programado'
        ];
    }

    // Verificar si una fecha es no laborable
    public static function esNoLaborable($fecha)
    {
        return self::where('fecha', $fecha)
            ->where('estado', 'activo')
            ->exists();
    }

    // Obtener motivo de bloqueo
    public static function getMotivoBloqueo($fecha)
    {
        $dia = self::where('fecha', $fecha)
            ->where('estado', 'activo')
            ->first();
        
        return $dia ? $dia->motivo : null;
    }

    // Scope para fechas activas
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}