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
        Schema::table('metodo_pagos', function (Blueprint $table) {
            // 1. Eliminar la columna 'divisa'
            $table->dropColumn('divisa');

            // 2. Añadir la clave foránea para la tabla 'cuentas'
            $table->foreignId('cuenta_id')->after('nombre')->constrained('cuentas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metodo_pagos', function (Blueprint $table) {
            // 1. Revertir la clave foránea
            $table->dropForeign(['cuenta_id']);
            $table->dropColumn('cuenta_id');

            // 2. Re-añadir la columna 'divisa'
            $table->string('divisa')->after('nombre');
        });
    }
};