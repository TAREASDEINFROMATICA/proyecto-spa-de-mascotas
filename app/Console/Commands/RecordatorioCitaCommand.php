<?php
namespace App\Console\Commands;

use App\Models\Cita;
use App\Mail\CitaMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use Carbon\Carbon;

class RecordatorioCitaCommand extends Command
{
    protected $signature = 'citas:recordatorio';
    protected $description = 'Envía recordatorios de citas 1 hora antes';

    public function handle()
    {
        $ahora = Carbon::now();
        $inicioRango = $ahora->copy()->addHour();
        $finRango = $ahora->copy()->addHour()->addMinutes(15);
        
        $citas = Cita::where('estado', 'programado')
            ->where('fecha', $ahora->toDateString())
            ->whereBetween('hora_inicio', [$inicioRango->format('H:i:s'), $finRango->format('H:i:s')])
            ->with(['mascota.cliente.usuario', 'servicio', 'empleado.usuario'])
            ->get();
        
        $this->info("Enviando recordatorios para " . $citas->count() . " citas");
        
        foreach ($citas as $cita) {
            try {
                $cliente = $cita->mascota->cliente;
                $email = $cliente->usuario->correo;
                Mail::to($email)->send(new CitaMail($cita, 'recordatorio'));
                $this->line("✅ Recordatorio enviado: {$cita->mascota->nombre} - {$cita->hora_inicio}");
            } catch (\Exception $e) {
                $this->error("❌ Error: {$cita->mascota->nombre} - " . $e->getMessage());
            }
        }
        
        return Command::SUCCESS;
    }
}