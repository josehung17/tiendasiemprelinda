<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $fillable = ['nombre', 'cuenta_id'];

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class);
    }
}