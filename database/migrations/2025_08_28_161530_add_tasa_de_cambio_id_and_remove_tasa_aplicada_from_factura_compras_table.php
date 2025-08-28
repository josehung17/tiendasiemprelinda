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
        Schema::table('factura_compras', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Asegurar que el motor sea InnoDB
            $table->foreignId('tasa_de_cambio_id')->nullable()->after('user_id');
            $table->foreign('tasa_de_cambio_id')->references('id')->on('tasas_de_cambio')->onDelete('set null');
            $table->dropColumn('tasa_cambio_aplicada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factura_compras', function (Blueprint $table) {
            $table->dropForeign(['tasa_de_cambio_id']);
            $table->dropColumn('tasa_de_cambio_id');
            $table->decimal('tasa_cambio_aplicada', 10, 4)->after('fecha_factura');
        });
    }
};