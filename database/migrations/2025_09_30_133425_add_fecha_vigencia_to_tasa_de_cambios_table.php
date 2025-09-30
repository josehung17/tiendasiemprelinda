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
            $table->date('fecha_vigencia')->after('tasa')->default(now())->comment('Fecha en que la tasa de cambio entra en vigencia');
            $table->index('fecha_vigencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasas_de_cambio', function (Blueprint $table) {
            $table->dropIndex(['fecha_vigencia']);
            $table->dropColumn('fecha_vigencia');
        });
    }
};
