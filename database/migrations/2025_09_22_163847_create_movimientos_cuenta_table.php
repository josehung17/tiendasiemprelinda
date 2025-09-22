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
        Schema::create('movimientos_cuenta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_id')->constrained('cuentas')->onDelete('cascade');
            $table->enum('tipo', ['entrada', 'salida']);
            $table->decimal('monto', 15, 2);
            $table->string('descripcion')->nullable();
            $table->foreignId('responsable_id')->constrained('users')->onDelete('restrict');
            $table->timestamp('fecha');
            $table->nullableMorphs('origen'); // origen_id, origen_type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_cuenta');
    }
};
