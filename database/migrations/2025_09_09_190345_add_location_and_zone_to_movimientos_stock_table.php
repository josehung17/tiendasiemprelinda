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
        Schema::table('movimientos_stock', function (Blueprint $table) {
            $table->foreignId('ubicacion_origen_id')->nullable()->constrained('ubicaciones')->onDelete('set null');
            $table->foreignId('zona_origen_id')->nullable()->constrained('zonas')->onDelete('set null');
            $table->foreignId('ubicacion_destino_id')->nullable()->constrained('ubicaciones')->onDelete('set null');
            $table->foreignId('zona_destino_id')->nullable()->constrained('zonas')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_stock', function (Blueprint $table) {
            $table->dropForeign(['ubicacion_origen_id']);
            $table->dropColumn('ubicacion_origen_id');
            $table->dropForeign(['zona_origen_id']);
            $table->dropColumn('zona_origen_id');
            $table->dropForeign(['ubicacion_destino_id']);
            $table->dropColumn('ubicacion_destino_id');
            $table->dropForeign(['zona_destino_id']);
            $table->dropColumn('zona_destino_id');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
