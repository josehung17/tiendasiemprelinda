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
        Schema::create('tasas_de_cambio', function (Blueprint $table) {
            $table->id();
            $table->string('moneda')->unique(); // Ej: 'USD'
            $table->decimal('tasa', 10, 4); // Tasa de cambio, ej: 1.2345
            $table->date('fecha_actualizacion'); // Fecha de la última actualización
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasas_de_cambio');
    }
};