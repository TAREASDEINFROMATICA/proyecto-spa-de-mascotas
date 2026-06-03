<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'estado_pedido')) {
                $table->enum('estado_pedido', [
                    'pendiente', 
                    'confirmado', 
                    'preparando', 
                    'listo_para_recoger', 
                    'entregado', 
                    'cancelado'
                ])->default('pendiente')->after('estado');
            }
            
            if (!Schema::hasColumn('ventas', 'fecha_listo_para_recoger')) {
                $table->timestamp('fecha_listo_para_recoger')->nullable()->after('estado_pedido');
            }
        });
    }

    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['estado_pedido', 'fecha_listo_para_recoger']);
        });
    }
};