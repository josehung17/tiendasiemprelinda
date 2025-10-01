<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaVenta extends Model
{
    use HasFactory;

    protected $table = 'facturas_venta';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'ubicacion_id',
        'tasa_de_cambio_id',
        'total_usd',
        'total_bs',
        'estado',
        'fecha_cliente_seleccionado',
        'fecha_borrador',
        'fecha_pago_parcial',
        'fecha_pago_total',
        'fecha_completado',
        'fecha_cancelado',
    ];

    protected $casts = [
        'fecha_cliente_seleccionado' => 'datetime',
        'fecha_borrador' => 'datetime',
        'fecha_pago_parcial' => 'datetime',
        'fecha_pago_total' => 'datetime',
        'fecha_completado' => 'datetime',
        'fecha_cancelado' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function tasaDeCambio()
    {
        return $this->belongsTo(TasaDeCambio::class);
    }

    public function detalles()
    {
        return $this->hasMany(FacturaVentaDetalle::class);
    }

    public function pagos()
    {
        return $this->hasMany(FacturaVentaPago::class);
    }
}