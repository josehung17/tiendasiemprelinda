<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaCompraMetodoPago extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_compra_id',
        'metodo_pago_id',
        'monto_usd',
    ];

    protected $casts = [
        'monto_usd' => 'decimal:2',
    ];

    public function facturaCompra()
    {
        return $this->belongsTo(FacturaCompra::class);
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }
}
