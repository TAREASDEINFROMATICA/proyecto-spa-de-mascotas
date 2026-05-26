<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $table = 'checklist_items';
    protected $primaryKey = 'id_item';
    public $timestamps = false; // Tu tabla no tiene timestamps

    protected $fillable = [
        'nombre',
        'requiere_observacion'
    ];

    protected $casts = [
        'requiere_observacion' => 'boolean'
    ];

    // Relación con servicios (muchos a muchos)
    public function servicios()
    {
        return $this->belongsToMany(
            Servicio::class, 
            'servicio_checklist', 
            'id_item', 
            'id_servicio'
        );
    }

    // Relación con ficha_checklist
    public function fichaChecklist()
    {
        return $this->hasMany(FichaChecklist::class, 'id_item', 'id_item');
    }
}