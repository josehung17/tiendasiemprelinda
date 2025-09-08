<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = 'ubicaciones';

    protected $fillable = [
        'nombre',
        'tipo',
        'direccion',
    ];

    /**
     * Get the zonas for the ubicacion.
     */
    public function zonas()
    {
        return $this->hasMany(Zona::class);
    }

    /**
     * The productos that belong to the ubicacion.
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_ubicacion')
                    ->withPivot('stock', 'stock_minimo', 'zona_id')
                    ->withTimestamps();
    }
}