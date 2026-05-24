<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->integer('capacidad_diaria')->default(8)->after('capacidad_simultanea');
        });
    }

    public function down()
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn('capacidad_diaria');
        });
    }
};