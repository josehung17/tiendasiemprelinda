<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaVentaPago extends Model
{
    use HasFactory;

    protected $table = 'factura_venta_pagos';

    protected $fillable = [
        'factura_venta_id',
        'metodo_pago_id',
        'monto_usd',
        'referencia',
    ];

    public function facturaVenta()
    {
        return $this->belongsTo(FacturaVenta::class);
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }
}