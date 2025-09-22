<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait OriginatesMovement
{
    /**
     * Get the short name of the class.
     */
    public function getShortClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * Get the URL to view/edit the originating document.
     */
    public function getViewUrl()
    {
        $className = $this->getShortClassName();

        return match ($className) {
            'FacturaCompra' => route('facturas-compra.edit', $this->id),
            // 'FacturaVenta' => route('facturas-venta.show', $this->id), // Example for future
            default => '#',
        };
    }
}
