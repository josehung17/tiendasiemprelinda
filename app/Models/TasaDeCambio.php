<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasaDeCambio extends Model
{
    protected $table = 'tasas_de_cambio'; // Especificar el nombre de la tabla
    protected $fillable = [
        'moneda',
        'tasa',
        'fecha_actualizacion',
        'fecha_vigencia',
    ];

    protected $casts = [
        'fecha_actualizacion' => 'datetime',
        'fecha_vigencia' => 'date',
    ];
}
