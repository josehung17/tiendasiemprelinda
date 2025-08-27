<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CategoriaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed'); // Asegurarse de que los seeders se ejecuten para tener roles y el usuario admin
        config()->set('permission.cache.enable', false); // Deshabilitar la caché de permisos de Spatie para tests
    }

    /** @test */
    public function admin_can_access_categoria_management_page()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $response = $this->get(route('categorias.index'));

        $response->assertStatus(200);
        $response->assertSee('Categorias');
        $response->assertSee('Crear Categoría');
    }

    /** @test */
    public function non_admin_cannot_access_categoria_management_page()
    {
        $user = User::factory()->create(); // Un usuario normal
        $this->actingAs($user);

        $response = $this->get(route('categorias.index'));

        $response->assertStatus(403); // O 302 si redirige a login, dependiendo de tu middleware
    }

    /** @test */
    public function admin_can_create_new_categoria()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $categoriaData = [
            'nombre' => 'Nueva Categoria Test',
            'ruta_imagen' => null, // Puedes simular la subida de archivos si es necesario
        ];

        $response = $this->post(route('categorias.store'), $categoriaData);

        $response->assertRedirect(route('categorias.index'));
        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Nueva Categoria Test',
        ]);
    }

    /** @test */
    public function admin_can_update_existing_categoria()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $categoriaToUpdate = Categoria::factory()->create([
            'nombre' => 'Categoria Antigua',
        ]);

        $updatedData = [
            'nombre' => 'Categoria Actualizada',
            'ruta_imagen' => null,
        ];

        $response = $this->put(route('categorias.update', $categoriaToUpdate->id), $updatedData);

        $response->assertRedirect(route('categorias.index'));
        $this->assertDatabaseHas('categorias', [
            'id' => $categoriaToUpdate->id,
            'nombre' => 'Categoria Actualizada',
        ]);
        $this->assertDatabaseMissing('categorias', [
            'nombre' => 'Categoria Antigua',
        ]);
    }

    /** @test */
    public function admin_can_delete_categoria()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $categoriaToDelete = Categoria::factory()->create([
            'nombre' => 'Categoria a Eliminar',
        ]);

        $response = $this->delete(route('categorias.destroy', $categoriaToDelete->id));

        $response->assertRedirect(route('categorias.index'));
        $this->assertDatabaseMissing('categorias', [
            'id' => $categoriaToDelete->id,
            'nombre' => 'Categoria a Eliminar',
        ]);
    }
}
