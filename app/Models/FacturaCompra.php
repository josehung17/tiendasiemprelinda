<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaCompra extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'fecha_factura',
        'tasa_cambio_aplicada',
        'total_usd',
        'total_bs',
        'user_id',
    ];

    protected $casts = [
        'fecha_factura' => 'date',
        'tasa_cambio_aplicada' => 'decimal:4',
        'total_usd' => 'decimal:2',
        'total_bs' => 'decimal:2',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(FacturaCompraDetalle::class);
    }

    public function metodosPago()
    {
        return $this->hasMany(FacturaCompraMetodoPago::class);
    }
}
