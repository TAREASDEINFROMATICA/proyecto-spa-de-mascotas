<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ficha_checklist', function (Blueprint $table) {
            if (!Schema::hasColumn('ficha_checklist', 'id_cita')) {
                $table->unsignedBigInteger('id_cita')->after('id_registro');
            }
            if (!Schema::hasColumn('ficha_checklist', 'id_empleado')) {
                $table->unsignedBigInteger('id_empleado')->after('id_cita');
            }
            if (!Schema::hasColumn('ficha_checklist', 'fecha_registro')) {
                $table->timestamp('fecha_registro')->nullable();
            }
            
            // Foreign keys
            if (!Schema::hasColumn('ficha_checklist', 'id_cita')) {
                $table->foreign('id_cita')->references('id_cita')->on('citas')->onDelete('cascade');
            }
            if (!Schema::hasColumn('ficha_checklist', 'id_empleado')) {
                $table->foreign('id_empleado')->references('id_empleado')->on('empleados')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('ficha_checklist', function (Blueprint $table) {
            $table->dropForeign(['id_cita']);
            $table->dropForeign(['id_empleado']);
            $table->dropColumn(['id_cita', 'id_empleado', 'fecha_registro']);
        });
    }
};