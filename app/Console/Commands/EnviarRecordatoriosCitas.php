<?php

namespace App\Console\Commands;

use App\Models\Cita;
use App\Http\Controllers\NotificacionController;
use Illuminate\Console\Command;
use Carbon\Carbon;

class EnviarRecordatoriosCitas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'citas:recordatorios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios de citas programadas para el día siguiente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fechaInicio = Carbon::tomorrow()->startOfDay();
        $fechaFin = Carbon::tomorrow()->endOfDay();
        
        $citas = Cita::where('estado', 'programado')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with(['mascota.cliente.usuario', 'servicio', 'empleado.usuario'])
            ->get();
        
        $this->info("📅 Enviando recordatorios para " . $citas->count() . " citas de mañana (" . Carbon::tomorrow()->format('d/m/Y') . ")");
        
        $enviados = 0;
        $errores = 0;
        
        foreach ($citas as $cita) {
            try {
                // Crear notificación en el sistema
                NotificacionController::crear(
                    $cita->mascota->cliente->id_usuario,
                    'cita_recordatorio',
                    "Recordatorio: Tienes una cita para {$cita->mascota->nombre} mañana {$cita->fecha} a las {$cita->hora_inicio}"
                );
                
                $this->line("  ✓ Recordatorio para: {$cita->mascota->nombre} - {$cita->hora_inicio}");
                $enviados++;
                
            } catch (\Exception $e) {
                $this->error("  ✗ Error: {$cita->mascota->nombre} - " . $e->getMessage());
                $errores++;
            }
        }
        
        $this->info("\n✅ Recordatorios enviados: {$enviados}");
        if ($errores > 0) {
            $this->warn("⚠️ Errores: {$errores}");
        }
        
        return Command::SUCCESS;
    }
}