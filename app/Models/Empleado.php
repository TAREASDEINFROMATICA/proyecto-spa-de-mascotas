<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'cargo',
        'especialidad',
        'capacidad_simultanea',
        'turno',      
        'fecha_ingreso'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function esGroomer()
    {
        return $this->cargo === 'Groomer';
    }

    public function esRecepcion()
    {
        return $this->cargo === 'Recepcion';
    }
    public function citas()
{
    return $this->hasMany(Cita::class, 'id_empleado');
}
}