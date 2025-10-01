<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factura_venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_venta_id')->constrained('facturas_venta')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('zona_id')->constrained('zonas');
            $table->integer('cantidad');
            $table->decimal('precio_unitario_usd', 10, 2);
            $table->decimal('subtotal_usd', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_venta_detalles');
    }
};