<?php
namespace App\Mail;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CitaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cita;
    public $tipo;

    public function __construct(Cita $cita, $tipo = 'solicitud')
    {
        $this->cita = $cita;
        $this->tipo = $tipo;
    }

    public function build()
    {
        $subject = match($this->tipo) {
            'solicitud' => '📋 Solicitud de Cita - Pet Spa',
            'confirmacion' => '✅ Cita Confirmada - Pet Spa',
            'recordatorio' => '⏰ Recordatorio de Cita - Pet Spa',
            default => 'Notificación de Cita - Pet Spa'
        };
        
        return $this->subject($subject)
                    ->view('emails.cita');
    }
}