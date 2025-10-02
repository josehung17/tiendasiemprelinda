<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Ubicacion;

class PosProductManager extends Component
{
    public $products = [];

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $locationId = session('pos_location_id');
        if (!$locationId) {
            $this->products = collect();
            return;
        }

        // Carga todos los productos que tienen stock en al menos una zona de la ubicación actual
        $this->products = Producto::select([
            'id', 'nombre', 'descripcion', 'precio', 'ruta_imagen', 'descuento', 
            'precio_descuento', 'nuevo', 'recomendado', 'categoria_id', 'marca_id'
        ])
        ->with([
            'categoria:id,nombre', 
            'marca:id,nombre',
            'zonasStock' => function ($query) use ($locationId) {
                // Eager load solo las zonas relevantes con stock
                $query->where('zonas.ubicacion_id', $locationId)->where('producto_ubicacion.stock', '>', 0);
            }
        ])
        ->whereHas('zonasStock', function ($query) use ($locationId) {
                $query->where('zonas.ubicacion_id', $locationId)->where('producto_ubicacion.stock', '>', 0);
            })
        ->orderBy('nombre')
        ->get()
        ->map(function ($product) {
            // Esta lógica determina qué zona y stock mostrar por defecto en la lista
            $defaultZoneStock = $product->zonasStock->firstWhere('pivot.es_zona_predeterminada_pos');

            if ($defaultZoneStock) {
                $product->stock_display = $defaultZoneStock->pivot->stock;
                $product->zone_id = $defaultZoneStock->id;
                $product->zone_name = $defaultZoneStock->nombre;
            } else {
                // Si no hay zona predeterminada con stock, toma la primera que encuentre con stock
                $firstZoneWithStock = $product->zonasStock->first();
                if ($firstZoneWithStock) {
                    $product->stock_display = $firstZoneWithStock->pivot->stock;
                    $product->zone_id = $firstZoneWithStock->id;
                    $product->zone_name = $firstZoneWithStock->nombre;
                } else {
                    $product->stock_display = 0;
                    $product->zone_id = null;
                    $product->zone_name = 'Sin stock';
                }
            }
            return $product;
        });
    }

    public function getZonasDisponiblesParaProducto(int $productId, int $ubicacionId): array
    {
        $product = Producto::find($productId);
        if (!$product) {
            return [];
        }

        $availableZones = $product->zonasStock()
                                  ->wherePivot('ubicacion_id', $ubicacionId)
                                  ->wherePivot('stock', '>', 0)
                                  ->get()
                                  ->map(function ($zone) {
                                      return [
                                          'id' => $zone->id,
                                          'nombre' => $zone->nombre,
                                          'stock' => $zone->pivot->stock,
                                      ];
                                  })
                                  ->toArray();

        return $availableZones;
    }

    public function render()
    {
        return view('livewire.pos-product-manager');
    }
}
