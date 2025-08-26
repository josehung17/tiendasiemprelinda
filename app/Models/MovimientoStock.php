<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    use HasFactory;

    protected $table = 'movimientos_stock';

    protected $fillable = [
        'producto_id',
        'tipo',
        'cantidad',
        'precio_compra_unitario',
        'proveedor_id',
        'referencia_venta',
        'motivo_ajuste',
    ];

    /**
     * Get the product that owns the stock movement.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Get the supplier that owns the stock movement.
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}