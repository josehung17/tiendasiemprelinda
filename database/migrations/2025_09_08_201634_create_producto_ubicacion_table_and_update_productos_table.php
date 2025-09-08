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
        // Crear la nueva tabla pivote para el stock distribuido
        Schema::create('producto_ubicacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('ubicacion_id')->constrained('ubicaciones')->onDelete('cascade');
            $table->foreignId('zona_id')->nullable()->constrained('zonas')->onDelete('set null');
            $table->integer('stock');
            $table->integer('stock_minimo')->default(0);
            $table->timestamps();

            // Para evitar duplicados
            $table->unique(['producto_id', 'ubicacion_id', 'zona_id']);
        });

        // Modificar la tabla de productos para eliminar la columna de stock antigua
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-añadir la columna de stock a la tabla de productos
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('precio');
        });

        // Eliminar la tabla pivote
        Schema::dropIfExists('producto_ubicacion');
    }
};