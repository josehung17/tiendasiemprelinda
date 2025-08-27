<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed'); // Asegurarse de que los seeders se ejecuten para tener roles y el usuario admin
        config()->set('permission.cache.enable', false); // Deshabilitar la caché de permisos de Spatie para tests
    }

    /** @test */
    public function admin_can_access_user_management_page()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('Usuarios');
        $response->assertSee('Crear Usuario');
    }

    /** @test */
    public function non_admin_cannot_access_user_management_page()
    {
        $user = User::factory()->create(); // Un usuario normal
        $this->actingAs($user);

        $response = $this->get(route('users.index'));

        $response->assertStatus(403); // O 302 si redirige a login, dependiendo de tu middleware
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $userData = [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post(route('users.store'), $userData);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@example.com',
            'name' => 'Nuevo Usuario',
        ]);
    }

    /** @test */
    public function admin_can_update_existing_user()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $userToUpdate = User::factory()->create([
            'name' => 'Usuario Antiguo',
            'email' => 'antiguo@example.com',
        ]);

        $updatedData = [
            'name' => 'Usuario Actualizado',
            'email' => 'actualizado@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->put(route('users.update', $userToUpdate->id), $updatedData);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $userToUpdate->id,
            'name' => 'Usuario Actualizado',
            'email' => 'actualizado@example.com',
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'antiguo@example.com',
        ]);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $userToDelete = User::factory()->create([
            'name' => 'Usuario a Eliminar',
            'email' => 'eliminar@example.com',
        ]);

        $response = $this->delete(route('users.destroy', $userToDelete->id));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id,
            'email' => 'eliminar@example.com',
        ]);
    }
}