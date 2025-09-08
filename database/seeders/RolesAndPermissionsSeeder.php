<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

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
            'access pos',
            'view metodos-pago',
            'create metodos-pago',
            'edit metodos-pago',
            'delete metodos-pago',
            'view tasa-de-cambio',
            'view facturas-compra',
            'create facturas-compra',
            'edit facturas-compra',
            'delete facturas-compra',
            'view ubicaciones',
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
                'access pos',
                'view metodos-pago',
                'create metodos-pago',
                'edit metodos-pago',
                'delete metodos-pago',
                'view tasa-de-cambio',
                'view facturas-compra',
                'create facturas-compra',
                'edit facturas-compra',
                'delete facturas-compra',
                'view ubicaciones',
            ],
            'editor' => [
                'edit products',
                'view products',
            ],
            'viewer' => [
                'view products',
            ],
            'vendedor' => [
                'access pos',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}