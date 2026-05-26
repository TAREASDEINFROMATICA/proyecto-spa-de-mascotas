<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServicioChecklist extends Model
{
    use HasFactory;

    protected $table = 'servicio_checklist';
    public $timestamps = false;

    protected $fillable = [
        'id_servicio',
        'id_item'
    ];
}