<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoMascota extends Model
{
    protected $table = 'fotos_mascota';
    protected $primaryKey = 'id_foto';
    public $timestamps = false; // Cambia a false si no tienes created_at/updated_at

    protected $fillable = [
        'id_ficha',
        'url',
        'tipo'
    ];

    // En app/Models/FotoMascota.php
public function fichaTecnica()
{
    return $this->belongsTo(FichaTecnica::class, 'id_ficha');
}

public function mascota()
{
    return $this->hasOneThrough(Mascota::class, FichaTecnica::class, 'id_ficha', 'id_mascota', 'id_ficha', 'id_mascota');
}
    
}