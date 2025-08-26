<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Support\Str; // 1. Importa la clase Str

class Producto extends Model
{
    use HasFactory; // Añadir esta línea

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'ruta_imagen',
        'slug',
        'nuevo',
        'recomendado',
        'descuento',
        'precio_descuento',
        'marca_id',
        'categoria_id',
        'stock',
        'precio_compra',
        'margen_ganancia'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // 2. Escucha el evento 'creating'
        static::creating(function ($producto) {
            // 3. Genera el slug a partir del nombre
            $baseSlug = Str::slug($producto->nombre);
            $slug = $baseSlug;
            $count = 1;

            while (static::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }

            $producto->slug = $slug;
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
}
