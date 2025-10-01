<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factura_venta_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_venta_id')->constrained('facturas_venta')->onDelete('cascade');
            $table->foreignId('metodo_pago_id')->constrained('metodo_pagos');
            $table->decimal('monto_usd', 10, 2);
            $table->string('referencia')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_venta_pagos');
    }
};