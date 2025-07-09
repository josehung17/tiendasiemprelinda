<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar el seeder de roles y permisos primero
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(MarcaSeeder::class);
        $this->call(ProductoSeeder::class);

        // Crear el usuario administrador
        $adminUser = User::factory()->create([
            'name' => 'jose hung',
            'email' => 'admin@siemprelinda.com',
            'password' => bcrypt('siemprelinda')
        ]);

        // Asignar el rol de 'admin'
        $adminRole = Role::findByName('admin');
        $adminUser->assignRole($adminRole);
    }
}
