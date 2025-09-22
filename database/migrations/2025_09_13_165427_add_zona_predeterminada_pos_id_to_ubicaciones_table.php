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
        Schema::table('ubicaciones', function (Blueprint $table) {
            $table->foreignId('zona_predeterminada_pos_id')
                  ->nullable()
                  ->after('direccion')
                  ->constrained('zonas')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ubicaciones', function (Blueprint $table) {
            $table->dropForeign(['zona_predeterminada_pos_id']);
            $table->dropColumn('zona_predeterminada_pos_id');
        });
    }
};
