<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ubicacion;
use App\Models\Zona;

class EnTransitoUbicacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ubicacion = Ubicacion::firstOrCreate(
            ['nombre' => 'En Transito'],
            ['descripcion' => 'Ubicación temporal para productos en transito', 'tipo' => 'transito']
        );

        Zona::firstOrCreate(
            ['nombre' => 'General', 'ubicacion_id' => $ubicacion->id],
            ['descripcion' => 'Zona general para productos en transito']
        );
    }
}