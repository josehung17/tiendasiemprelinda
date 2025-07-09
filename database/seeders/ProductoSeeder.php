<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriaIds = Categoria::all()->pluck('id')->toArray();
        $marcaIds = Marca::all()->pluck('id')->toArray();

        Producto::factory()->count(100)->create([
            'categoria_id' => function () use ($categoriaIds) {
                return $categoriaIds[array_rand($categoriaIds)];
            },
            'marca_id' => function () use ($marcaIds) {
                return $marcaIds[array_rand($marcaIds)];
            },
        ]);
    }
}
