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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->string('ruta_imagen')->nullable();
            $table->boolean('nuevo')->nullable();
            $table->boolean('recomendado');
            $table->boolean('descuento')->nullable();
            $table->decimal('precio_descuento', 10, 2)->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('marca_id')->nullable();
            $table->unsignedBigInteger('categoria_id')->foreign('categorias')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
