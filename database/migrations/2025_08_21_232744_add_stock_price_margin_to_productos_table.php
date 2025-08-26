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
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('precio');
            $table->decimal('precio_compra', 10, 2)->nullable()->after('stock');
            $table->decimal('margen_ganancia', 5, 2)->nullable()->after('precio_compra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['stock', 'precio_compra', 'margen_ganancia']);
        });
    }
};