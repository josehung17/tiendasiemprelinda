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
        Schema::create('factura_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->date('fecha_factura');
            $table->decimal('tasa_cambio_aplicada', 10, 4); // Tasa de cambio aplicada en el momento de la factura
            $table->decimal('total_usd', 10, 2);
            $table->decimal('total_bs', 10, 2);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario que registra la factura
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_compras');
    }
};
