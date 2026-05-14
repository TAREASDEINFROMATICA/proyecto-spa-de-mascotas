<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CredencialesEmpleadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $contrasena;

    public function __construct(Usuario $usuario, $contrasena)
    {
        $this->usuario = $usuario;
        $this->contrasena = $contrasena;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tus credenciales - Pet Spa',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.credenciales-empleado',
        );
    }
}