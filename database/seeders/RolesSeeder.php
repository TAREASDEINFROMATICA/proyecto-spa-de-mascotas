<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Insertar roles
        DB::table('roles')->insert([
            ['nombre' => 'Administrador', 'descripcion' => 'Acceso total al sistema'],
            ['nombre' => 'Recepcion', 'descripcion' => 'Gestiona citas y atención al cliente'],
            ['nombre' => 'Groomer', 'descripcion' => 'Realiza servicios de estética y baño'],
            ['nombre' => 'Cliente', 'descripcion' => 'Usuario dueño de mascotas'],
        ]);

        // Crear usuario administrador
        $adminRolId = DB::table('roles')->where('nombre', 'Administrador')->first()->id_rol;
        
        DB::table('usuarios')->insert([
            'id_rol' => $adminRolId,
            'nombres' => 'Admin',
            'apellidos' => 'Principal',
            'correo' => 'admin@spamascota.com',
            'contrasena_hash' => Hash::make('Admin123'),
            'telefono' => '77777777',
            'estado' => 'activo',
            'email_verified_at' => Carbon::now(),
            'fecha_registro' => Carbon::now(),
        ]);
    }
}