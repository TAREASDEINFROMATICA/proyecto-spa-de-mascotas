<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FA\Google2FA;
class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;  // ← AGREGA ESTA LÍNEA
    
    protected $fillable = [
        'id_rol',
        'nombres',
        'apellidos',
        'correo',
        'ci',
        'contrasena_hash',
        'telefono',
        'estado',
        'email_verified_at',
        'verification_token',
        'verification_token_expires_at',
        'reset_token',
        'reset_token_expires_at',
        'login_attempts',
        'blocked_until',
        'two_factor_secret',
        'last_activity_at',
    ];

    protected $hidden = [
        'contrasena_hash',
        'verification_token',
        'reset_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_registro' => 'datetime',
        'blocked_until' => 'datetime',
    ];

    // Método necesario para Laravel
    public function getAuthPassword()
    {
        return $this->contrasena_hash;
    }

    public function getEmailForVerification()
    {
        return $this->correo;
    }

    // Relaciones
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id_usuario');
    }
/*
    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'id_usuario');
    }
*/
    // Verificación de roles
    public function esAdmin()
    {
        return $this->rol->nombre === 'Administrador';
    }

    public function esPersonal()
    {
        return in_array($this->rol->nombre, ['Recepcion', 'Groomer']);
    }

    public function esCliente()
    {
        return $this->rol->nombre === 'Cliente';
    }
    
public function getGoogle2faSecretAttribute($value)
{
    return $value;
}

public function setGoogle2faSecretAttribute($value)
{
    $this->attributes['two_factor_secret'] = $value;
}

// Verificar si el usuario tiene 2FA activado
public function tiene2FA()
{
    return !is_null($this->two_factor_secret);
}

// Generar nuevo secreto para 2FA
public function generarSecreto2FA()
{
    $google2fa = new Google2FA();
    $secreto = $google2fa->generateSecretKey();
    $this->two_factor_secret = $secreto;
    $this->save();
    return $secreto;
}

// Verificar código 2FA
public function verificarCodigo2FA($codigo)
{
    if (!$this->tiene2FA()) {
        return true;
    }
    
    $google2fa = new Google2FA();
    return $google2fa->verifyKey($this->two_factor_secret, $codigo);
}
}