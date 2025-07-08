<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'ruta_imagen', 'descripcion'];
    
    /**
     * Get the products associated with the category.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
