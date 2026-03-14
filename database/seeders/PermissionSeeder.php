<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// IMPORTANTE: Usar el modelo de Spatie
use Spatie\Permission\Models\Permission; 

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 🔴 CAMBIO: Limpiar la caché de Spatie para evitar conflictos de permisos viejos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 🔴 CAMBIO: Lista unificada de permisos según tu matriz
        $permissions = [
            // Módulo de Usuarios
            'view_users', 
            'register_user', 
            'edit_user', 
            'delete_user',

            // Módulo de Cursos
            'view_courses', 
            'register_courses', 
            'edit_courses', 
            'delete_courses',

            // Módulo de Categorías
            'view_categories', 
            'register_categories', 
            'edit_categories', 
            'delete_categories',

            // Otros Módulos
            'manage_about', 
            'manage_coupons', 
            'manage_discounts',
            
            // Módulo de Roles
            'view_role', 
            'register_role', 
            'edit_role', 
            'delete_role',
        ];

        // 🔴 CAMBIO: Recorremos el array e insertamos con guard_name 'api'
        foreach ($permissions as $permissionName) {
            Permission::updateOrCreate(
                [
                    'name' => $permissionName, 
                    'guard_name' => 'api'
                ],
                [
                    'name' => $permissionName, 
                    'guard_name' => 'api'
                ]
            );
        }
    }
}