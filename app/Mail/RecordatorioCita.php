<?php
namespace App\Mail;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecordatorioCitaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cita;
    public $tipo;

    public function __construct(Cita $cita, $tipo = 'recordatorio')
    {
        $this->cita = $cita;
        $this->tipo = $tipo;
    }

    public function build()
    {
        $subject = $this->tipo == 'recordatorio' 
            ? '📅 Recordatorio de tu cita - Pet Spa' 
            : '✅ Tu cita ha sido confirmada - Pet Spa';
            
        return $this->subject($subject)
                    ->view('emails.cita-notificacion');
    }
}