<?php
namespace App\Mail;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionClienteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cita;
    public $tipo;
    public $mensaje;

    public function __construct(Cita $cita, $tipo, $mensaje)
    {
        $this->cita = $cita;
        $this->tipo = $tipo;
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        $subject = $this->tipo == 'servicio_finalizado' 
            ? '🏁 Tu servicio ha sido completado - Pet Spa' 
            : '📢 Notificación - Pet Spa';
            
        return $this->subject($subject)
                    ->view('emails.notificacion-cliente');
    }
}