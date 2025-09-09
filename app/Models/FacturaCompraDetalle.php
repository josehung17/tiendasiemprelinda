<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaCompraDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_compra_id',
        'producto_id',
        'cantidad',
        'precio_compra_unitario',
        'subtotal_usd',
        'ubicacion_id',
        'zona_id',
    ];

    protected $casts = [
        'precio_compra_unitario' => 'decimal:2',
        'subtotal_usd' => 'decimal:2',
    ];

    public function facturaCompra()
    {
        return $this->belongsTo(FacturaCompra::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
