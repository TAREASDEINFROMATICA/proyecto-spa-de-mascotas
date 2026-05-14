<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mascota extends Model
{
    protected $table = 'mascotas';
    protected $primaryKey = 'id_mascota';
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'nombre',
        'especie',
        'raza',
        'sexo',
        'fecha_nacimiento',
        'peso',
        'color',
        'temperamento_general',
        'alergias',
        'cuidados_especiales',
        'observaciones',
        'foto',
        'estado'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'peso' => 'decimal:2'
    ];

    // Relación con el cliente
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    // Obtener edad de la mascota
    public function getEdadAttribute()
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }
        return $this->fecha_nacimiento->age;
    }

    // Scope para mascotas activas
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    // Verificar si está activa
    public function isActiva()
    {
        return $this->estado === 'activa';
    }
}