<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCuenta extends Model
{
    use HasFactory;

    protected $table = 'movimientos_cuenta';

    protected $fillable = [
        'cuenta_id',
        'tipo',
        'monto',
        'descripcion',
        'responsable_id',
        'fecha',
        'origen_id',
        'origen_type',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function origen()
    {
        return $this->morphTo();
    }
}