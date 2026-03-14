<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. USUARIO ADMINISTRADOR
        $admin = User::create([
            'name'      => 'Admin',
            'surname'   => 'Sistema',
            'email'     => 'admin@gmail.com',
            'password'  => Hash::make('12345678'),
            'type_user' => 1,        // 1 = Interno/Admin
            'state'     => 1,        // Activo
            'avatar'    => 'default.png',
        ]);
        // 🔴 ASIGNACIÓN: Se vincula con los 19 permisos que vimos en tu consola
        $admin->assignRole('Administrador');


        // 2. USUARIO PROFESOR (Instuctor)
        $profesor = User::create([
            'name'      => 'Juan',
            'surname'   => 'Profesor',
            'email'     => 'profe@gmail.com',
            'password'  => Hash::make('12345678'),
            'type_user' => 1,        // Interno
            'is_instructor' => 1,    // Campo para identificar instructores
            'state'     => 1,
            'avatar'    => 'default.png',
        ]);
        // 🔴 ASIGNACIÓN: Solo tendrá acceso a Cursos según tu matriz
        $profesor->assignRole('Profesor');


        // 3. USUARIO CLIENTE (Estudiante)
        $cliente = User::create([
            'name'      => 'Luis',
            'surname'   => 'Alumno',
            'email'     => 'cliente@gmail.com',
            'password'  => Hash::make('12345678'),
            'type_user' => 2,        // 2 = Externo/Cliente
            'state'     => 1,
            'avatar'    => 'default.png',
        ]);
        // 🔴 ASIGNACIÓN: No tendrá acceso al panel administrativo (Dashboard vacío)
        $cliente->assignRole('Cliente');
    }
}