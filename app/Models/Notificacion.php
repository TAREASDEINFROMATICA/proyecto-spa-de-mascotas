<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';
    public $timestamps = false;
    
    protected $fillable = [
        'id_usuario',
        'tipo',
        'mensaje',
        'fecha_envio',
        'estado'
    ];
    
    protected $casts = [
        'fecha_envio' => 'datetime'
    ];
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}