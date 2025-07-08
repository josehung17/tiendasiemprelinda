<?php

namespace Database\Factories;

use App\Models\Marca;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->words(3, true),
            'descripcion' => $this->faker->paragraph(),
            'precio' => $this->faker->randomFloat(2, 10, 1000),
            'precio_descuento' => $this->faker->optional(0.5)->randomFloat(2, 5, 900),
            'ruta_imagen' => null, // O puedes generar una ruta de imagen falsa si es necesario
            'nuevo' => $this->faker->boolean(),
            'recomendado' => $this->faker->boolean(),
            'descuento' => $this->faker->boolean(),
            'marca_id' => Marca::factory(), // Crea una marca si no existe
            'categoria_id' => Categoria::factory(), // Crea una categoría si no existe
        ];
    }
}
