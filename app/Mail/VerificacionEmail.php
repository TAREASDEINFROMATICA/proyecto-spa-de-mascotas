<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificacionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $token;
    public $enlace;

    public function __construct(Usuario $usuario, $token)
    {
        $this->usuario = $usuario;
        $this->token = $token;
        $this->enlace = url("/verificar-email/{$token}");
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verifica tu cuenta - Pet Spa',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verificacion',
        );
    }
}