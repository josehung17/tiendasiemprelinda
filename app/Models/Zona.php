<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'ubicacion_id',
        'descripcion',
    ];

    /**
     * Get the ubicacion that owns the zona.
     */
    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }
}