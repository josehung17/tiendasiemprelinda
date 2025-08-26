<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('¡Ejecutando el RolesAndPermissionsSeeder!');
        //reset the roles and permissions
        app()['cache']->forget('spatie.permission.cache');
        // Crear permisos
        $permissions = [
            'create products',
            'edit products',
            'delete products',
            'view products',
            'create categories',
            'edit categories',
            'delete categories',
            'view categories',
            'create users',
            'edit users',
            'delete users',
            'view users',
            'create marcas',
            'edit marcas',
            'delete marcas',
            'view marcas',
            'create clients',
            'edit clients',
            'delete clients',
            'view clients',
            'create proveedores',
            'edit proveedores',
            'delete proveedores',
            'view proveedores',
            'manage stock',
            'assign roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $roles = [
            'admin' => [
                'create products',
                'edit products',
                'delete products',
                'view products',
                'create categories',
                'edit categories',
                'delete categories',
                'view categories',
                'create users',
                'edit users',
                'delete users',
                'view users',
                'create marcas',
                'edit marcas',
                'delete marcas',
                'view marcas',
                'create clients',
                'edit clients',
                'delete clients',
                'view clients',
                'create proveedores',
                'edit proveedores',
                'delete proveedores',
                'view proveedores',
                'manage stock',
                'assign roles',
            ],
            'editor' => [
                'edit products',
                'view products',
            ],
            'viewer' => [
                'view products',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}