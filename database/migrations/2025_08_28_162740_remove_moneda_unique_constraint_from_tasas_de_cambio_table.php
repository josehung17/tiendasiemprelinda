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
        Schema::table('tasas_de_cambio', function (Blueprint $table) {
            $table->dropUnique(['moneda']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasas_de_cambio', function (Blueprint $table) {
            $table->string('moneda')->unique()->change();
        });
    }
};