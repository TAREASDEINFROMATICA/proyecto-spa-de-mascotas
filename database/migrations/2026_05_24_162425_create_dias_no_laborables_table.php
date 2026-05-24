<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dias_no_laborables', function (Blueprint $table) {
            $table->id('id_dia_no_laborable');
            $table->date('fecha');
            $table->string('tipo', 30); // feriado, mantenimiento, ausencia, descanso
            $table->string('motivo', 200)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            
            // Índice único para evitar duplicados
            $table->unique('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dias_no_laborables');
    }
};