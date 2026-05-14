<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSistema extends Model
{
    protected $table = 'log_sistema';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'accion',
        'fecha',
        'ip_address',
        'user_agent'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}