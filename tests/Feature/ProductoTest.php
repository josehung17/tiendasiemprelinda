<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed'); // Asegurarse de que los seeders se ejecuten para tener roles y el usuario admin
        config()->set('permission.cache.enable', false); // Deshabilitar la caché de permisos de Spatie para tests
    }

    /** @test */
    public function admin_can_access_producto_management_page()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $response = $this->get(route('productos.index'));

        $response->assertStatus(200);
        $response->assertSee('Productos');
        $response->assertSee('Crear Producto');
    }

    /** @test */
    public function non_admin_cannot_access_producto_management_page()
    {
        $user = User::factory()->create(); // Un usuario normal
        $this->actingAs($user);

        $response = $this->get(route('productos.index'));

        $response->assertStatus(403); // O 302 si redirige a login, dependiendo de tu middleware
    }

    /** @test */
    public function admin_can_create_new_producto()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        // Crear una marca y categoría para asociar al producto
        $marca = Marca::factory()->create();
        $categoria = Categoria::factory()->create();

        $productoData = [
            'nombre' => 'Nuevo Producto Test',
            'marca_id' => $marca->id,
            'categoria_id' => $categoria->id,
            'descripcion' => 'Descripción del nuevo producto.',
            'precio' => 100.00,
            'precio_descuento' => 90.00,
            'ruta_imagen' => null,
            'nuevo' => true,
            'recomendado' => true,
            'descuento' => true,
        ];

        $response = $this->post(route('productos.store'), $productoData);

        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Nuevo Producto Test',
            'precio' => 100.00,
        ]);
    }

    /** @test */
    public function admin_can_update_existing_producto()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $marca = Marca::factory()->create();
        $categoria = Categoria::factory()->create();

        $productoToUpdate = Producto::factory()->create([
            'nombre' => 'Producto Antiguo',
            'marca_id' => $marca->id,
            'categoria_id' => $categoria->id,
        ]);

        $updatedData = [
            'nombre' => 'Producto Actualizado',
            'marca_id' => $marca->id,
            'categoria_id' => $categoria->id,
            'descripcion' => 'Descripción actualizada.',
            'precio' => 120.00,
            'precio_descuento' => 110.00,
            'ruta_imagen' => null,
            'nuevo' => false,
            'recomendado' => false,
            'descuento' => false,
        ];

        $response = $this->put(route('productos.update', $productoToUpdate->id), $updatedData);

        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('productos', [
            'id' => $productoToUpdate->id,
            'nombre' => 'Producto Actualizado',
            'precio' => 120.00,
        ]);
        $this->assertDatabaseMissing('productos', [
            'nombre' => 'Producto Antiguo',
        ]);
    }

    /** @test */
    public function admin_can_delete_producto()
    {
        $admin = User::where('email', 'admin@siemprelinda.com')->first();
        $this->actingAs($admin);

        $productoToDelete = Producto::factory()->create([
            'nombre' => 'Producto a Eliminar',
        ]);

        $response = $this->delete(route('productos.destroy', $productoToDelete->id));

        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseMissing('productos', [
            'id' => $productoToDelete->id,
            'nombre' => 'Producto a Eliminar',
        ]);
    }
}
