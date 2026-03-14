<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Limpiamos la caché de permisos para evitar conflictos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Creamos/Actualizamos los roles con el guard 'api'
        $admin = Role::updateOrCreate(
            ['id' => 1], 
            ['name' => 'Administrador', 'guard_name' => 'api']
        );

        $cliente = Role::updateOrCreate(
            ['id' => 2], 
            ['name' => 'Cliente', 'guard_name' => 'api']
        );

        $profesor = Role::updateOrCreate(
            ['id' => 3], 
            ['name' => 'Profesor', 'guard_name' => 'api']
        );

        // 2. ASIGNACIÓN DE PERMISOS PARA EL ADMINISTRADOR
        // 🔴 CAMBIO: Asignamos TODOS los permisos disponibles al Admin
        $allPermissions = Permission::where('guard_name', 'api')->get();
        $admin->syncPermissions($allPermissions);

        // 3. ASIGNACIÓN DE PERMISOS PARA EL PROFESOR
        // 🔴 CAMBIO: Asignamos solo los permisos de la matriz para el profesor
        $profesor->syncPermissions([
            'view_courses', 
            'register_courses', 
            'edit_courses', 
            'delete_courses'
        ]);

        // 4. ASIGNACIÓN DE PERMISOS PARA EL CLIENTE
        // 🔴 CAMBIO: Se queda sin permisos de panel (array vacío)
        $cliente->syncPermissions([]);

        // 5. Vincular al primer usuario (Opcional si usas UserSeeder después)
        $user = User::first(); 
        if ($user) {
            $user->assignRole($admin);
        }
    }
}