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
        Schema::table('factura_compra_detalles', function (Blueprint $table) {
            $table->foreignId('ubicacion_id')->after('subtotal_usd')->nullable()->constrained('ubicaciones')->onDelete('set null');
            $table->foreignId('zona_id')->after('ubicacion_id')->nullable()->constrained('zonas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factura_compra_detalles', function (Blueprint $table) {
            $table->dropForeign(['ubicacion_id']);
            $table->dropForeign(['zona_id']);
            $table->dropColumn(['ubicacion_id', 'zona_id']);
        });
    }
};
