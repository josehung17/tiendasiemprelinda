<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaVentaDetalle extends Model
{
    use HasFactory;

    protected $table = 'factura_venta_detalles';

    protected $fillable = [
        'factura_venta_id',
        'producto_id',
        'zona_id',
        'cantidad',
        'precio_unitario_usd',
        'subtotal_usd',
    ];

    public function facturaVenta()
    {
        return $this->belongsTo(FacturaVenta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}