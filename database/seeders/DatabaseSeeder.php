<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    // database/seeders/DatabaseSeeder.php
public function run()
    {
        $this->call([
            PermissionSeeder::class, // 1. Crea las acciones (llaves)
            RoleSeeder::class,       // 2. Crea los Roles y les pega los permisos (llaveros)
            UserSeeder::class,       // 3. Crea los Usuarios y les asigna el Rol (dueños)    
           
        ]);
    }

}
