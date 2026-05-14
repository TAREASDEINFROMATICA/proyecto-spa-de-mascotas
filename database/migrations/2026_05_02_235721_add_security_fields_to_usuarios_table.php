<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Verificación de email
            $table->timestamp('email_verified_at')->nullable()->after('fecha_registro');
            $table->string('verification_token')->nullable()->after('email_verified_at');
            $table->timestamp('verification_token_expires_at')->nullable()->after('verification_token');
            
            // Recuperación de contraseña
            $table->string('reset_token')->nullable()->after('verification_token_expires_at');
            $table->timestamp('reset_token_expires_at')->nullable()->after('reset_token');
            
            // Control de intentos fallidos
            $table->integer('login_attempts')->default(0)->after('reset_token_expires_at');
            $table->timestamp('blocked_until')->nullable()->after('login_attempts');
            
            // 2FA y actividad
            $table->string('two_factor_secret')->nullable()->after('blocked_until');
            $table->timestamp('last_activity_at')->nullable()->after('two_factor_secret');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn([
                'email_verified_at',
                'verification_token',
                'verification_token_expires_at',
                'reset_token',
                'reset_token_expires_at',
                'login_attempts',
                'blocked_until',
                'two_factor_secret',
                'last_activity_at'
            ]);
        });
    }
};