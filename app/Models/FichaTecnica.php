<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FichaTecnica extends Model
{
    protected $table = 'ficha_tecnica';
    protected $primaryKey = 'id_ficha';
    public $timestamps = false;
    
    protected $fillable = [
        'id_cita',
        'estado_ingreso',
        'temperamento_observado',
        'temperatura_corporal',
        'observacion_temperamento',
        'recomendaciones',
        'detalles_servicio',
        'fecha_apertura',
        'fecha_cierre'
    ];
    
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita');
    }
    
    public function fotos()
    {
        return $this->hasMany(FotoMascota::class, 'id_ficha');
    }
}