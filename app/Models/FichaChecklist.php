<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FichaChecklist extends Model
{
    use HasFactory;

    protected $table = 'ficha_checklist';
    protected $primaryKey = 'id_registro';
    public $timestamps = false;

    protected $fillable = [
        'id_ficha',
        'id_cita',
        'id_empleado',
        'id_item',
        'realizado',
        'observacion',
        'fecha_registro'
    ];

    protected $casts = [
        'realizado' => 'boolean',
        'fecha_registro' => 'datetime'
    ];

    // Relación con cita
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    // Relación con empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }

    // Relación con checklist item
    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class, 'id_item', 'id_item');
    }
}