<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualiza los registros existentes para que la fecha_vigencia sea un día antes que la fecha_actualizacion
        \Illuminate\Support\Facades\DB::table('tasas_de_cambio')->update([
            'fecha_vigencia' => \Illuminate\Support\Facades\DB::raw('DATE(DATE_SUB(fecha_actualizacion, INTERVAL 1 DAY))')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Esta es una corrección de datos, no se necesita rollback.
        // Dejar vacío intencionadamente.
    }
};
