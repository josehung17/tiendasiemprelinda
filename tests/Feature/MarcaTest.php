<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Marca;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MarcaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed'); // Asegurarse de que los seeders se ejecuten para tener roles y el usuario admin
        config()->set('permission.cache.enable', false); // Deshabilitar la caché de permisos de Spatie para tests
    }

    /** @test */
    public function admin_can_access_marca_management_page()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $response = $this->get(route('marcas.index'));

        $response->assertStatus(200);
        $response->assertSee('Marcas');
        $response->assertSee('Crear Marca');
    }

    /** @test */
    public function non_admin_cannot_access_marca_management_page()
    {
        $user = User::factory()->create(); // Un usuario normal
        $this->actingAs($user);

        $response = $this->get(route('marcas.index'));

        $response->assertStatus(403); // O 302 si redirige a login, dependiendo de tu middleware
    }

    /** @test */
    public function admin_can_create_new_marca()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $marcaData = [
            'nombre' => 'Nueva Marca Test',
            'ruta_imagen' => null, // Puedes simular la subida de archivos si es necesario
        ];

        $response = $this->post(route('marcas.store'), $marcaData);

        $response->assertRedirect(route('marcas.index'));
        $this->assertDatabaseHas('marcas', [
            'nombre' => 'Nueva Marca Test',
        ]);
    }

    /** @test */
    public function admin_can_update_existing_marca()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $marcaToUpdate = Marca::factory()->create([
            'nombre' => 'Marca Antigua',
        ]);

        $updatedData = [
            'nombre' => 'Marca Actualizada',
            'ruta_imagen' => null,
        ];

        $response = $this->put(route('marcas.update', $marcaToUpdate->id), $updatedData);

        $response->assertRedirect(route('marcas.index'));
        $this->assertDatabaseHas('marcas', [
            'id' => $marcaToUpdate->id,
            'nombre' => 'Marca Actualizada',
        ]);
        $this->assertDatabaseMissing('marcas', [
            'nombre' => 'Marca Antigua',
        ]);
    }

    /** @test */
    public function admin_can_delete_marca()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $marcaToDelete = Marca::factory()->create([
            'nombre' => 'Marca a Eliminar',
        ]);

        $response = $this->delete(route('marcas.destroy', $marcaToDelete->id));

        $response->assertRedirect(route('marcas.index'));
        $this->assertDatabaseMissing('marcas', [
            'id' => $marcaToDelete->id,
            'nombre' => 'Marca a Eliminar',
        ]);
    }
}
