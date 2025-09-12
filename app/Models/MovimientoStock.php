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
        'ubicacion_origen_id',
        'zona_origen_id',
        'ubicacion_destino_id',
        'zona_destino_id',
        'user_id',
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

    /**
     * Get the origin location for the stock movement.
     */
    public function ubicacionOrigen()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_origen_id');
    }

    /**
     * Get the origin zone for the stock movement.
     */
    public function zonaOrigen()
    {
        return $this->belongsTo(Zona::class, 'zona_origen_id');
    }

    /**
     * Get the destination location for the stock movement.
     */
    public function ubicacionDestino()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_destino_id');
    }

    /**
     * Get the destination zone for the stock movement.
     */
    public function zonaDestino()
    {
        return $this->belongsTo(Zona::class, 'zona_destino_id');
    }

    /**
     * Get the user that performed the stock movement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}