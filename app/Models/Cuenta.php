<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'datos_adicionales', 'moneda_id'];

    public function moneda()
    {
        return $this->belongsTo(Moneda::class);
    }

    public function metodosPago()
    {
        return $this->hasMany(MetodoPago::class);
    }
}