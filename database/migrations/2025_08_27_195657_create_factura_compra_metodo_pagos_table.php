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
        Schema::create('factura_compra_metodo_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_compra_id')->constrained('factura_compras')->onDelete('cascade');
            $table->foreignId('metodo_pago_id')->constrained('metodo_pagos')->onDelete('cascade');
            $table->decimal('monto_usd', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_compra_metodo_pagos');
    }
};
