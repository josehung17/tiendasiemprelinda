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
        // Añadir la nueva columna a la tabla pivote
        Schema::table('producto_ubicacion', function (Blueprint $table) {
            $table->boolean('es_zona_predeterminada_pos')->default(false)->after('stock_minimo');
        });

        // Eliminar la columna antigua de la tabla de ubicaciones
        // Nos aseguramos de que la tabla y la columna existan antes de intentar eliminarlas
        if (Schema::hasTable('ubicaciones') && Schema::hasColumn('ubicaciones', 'zona_predeterminada_pos_id')) {
            Schema::table('ubicaciones', function (Blueprint $table) {
                // El nombre de la restricción puede variar, Laravel a menudo lo genera automáticamente.
                // Una forma más robusta es obtener el nombre de la restricción del esquema.
                // Sin embargo, para este caso, supondremos el nombre estándar o simplemente eliminaremos la columna,
                // lo que en muchos sistemas de BD también elimina la restricción.
                // Para mayor compatibilidad, primero eliminamos la FK por su convención de nombre.
                $table->dropForeign(['zona_predeterminada_pos_id']);
                $table->dropColumn('zona_predeterminada_pos_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir la eliminación de la columna en ubicaciones
        if (Schema::hasTable('ubicaciones') && !Schema::hasColumn('ubicaciones', 'zona_predeterminada_pos_id')) {
            Schema::table('ubicaciones', function (Blueprint $table) {
                $table->foreignId('zona_predeterminada_pos_id')
                      ->nullable()
                      ->after('direccion')
                      ->constrained('zonas')
                      ->nullOnDelete();
            });
        }

        // Revertir la adición de la columna en la tabla pivote
        if (Schema::hasTable('producto_ubicacion') && Schema::hasColumn('producto_ubicacion', 'es_zona_predeterminada_pos')) {
            Schema::table('producto_ubicacion', function (Blueprint $table) {
                $table->dropColumn('es_zona_predeterminada_pos');
            });
        }
    }
};