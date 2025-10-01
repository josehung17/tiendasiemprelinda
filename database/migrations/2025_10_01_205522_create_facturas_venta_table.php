<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->foreignId('tasa_de_cambio_id')->constrained('tasas_de_cambio');
            $table->decimal('total_usd', 10, 2);
            $table->decimal('total_bs', 10, 2);
            $table->string('estado')->default('borrador');
            $table->dateTime('fecha_cliente_seleccionado')->nullable();
            $table->dateTime('fecha_borrador')->nullable();
            $table->dateTime('fecha_pago_parcial')->nullable();
            $table->dateTime('fecha_pago_total')->nullable();
            $table->dateTime('fecha_completado')->nullable();
            $table->dateTime('fecha_cancelado')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas_venta');
    }
};