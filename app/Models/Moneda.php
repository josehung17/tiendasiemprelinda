<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'simbolo', 'codigo'];

    public function cuentas()
    {
        return $this->hasMany(Cuenta::class);
    }
}