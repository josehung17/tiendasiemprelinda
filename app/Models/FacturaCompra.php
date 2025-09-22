<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\OriginatesMovement;

class FacturaCompra extends Model
{
    use HasFactory, OriginatesMovement;

    protected $fillable = [
        'proveedor_id',
        'fecha_factura',
        'tasa_de_cambio_id',
        'total_usd',
        'total_bs',
        'user_id',
    ];

    protected $casts = [
        'fecha_factura' => 'date',
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

    public function pagos()
    {
        return $this->hasMany(FacturaCompraMetodoPago::class);
    }

    public function tasaDeCambio()
    {
        return $this->belongsTo(TasaDeCambio::class);
    }
}
