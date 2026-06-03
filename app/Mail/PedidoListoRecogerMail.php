<?php

namespace App\Mail;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoListoRecogerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $venta;
    public $cliente;

    public function __construct(Venta $venta)
    {
        $this->venta = $venta;
        $this->cliente = $venta->cliente;
    }

    public function build()
    {
        return $this->subject('📦 Tu pedido está listo para recoger - Pet Spa')
                    ->view('emails.pedido-listo-recoger');
    }
}