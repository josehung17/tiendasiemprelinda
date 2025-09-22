<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;

class PosProductManager extends Component
{
    public $searchTerm = '';
    public $quantities = [];

    public function getSearchResultsProperty()
    {
        if (strlen($this->searchTerm) < 2) {
            return collect();
        }

        $locationId = session('pos_location_id');

        if (!$locationId) {
            return collect();
        }

        return Producto::where('nombre', 'like', '%' . $this->searchTerm . '%')
            ->whereHas('ubicaciones', function ($query) use ($locationId) {
                $query->where('ubicacion_id', $locationId)->where('stock', '>', 0);
            })
            ->with(['zonasStock' => function ($query) use ($locationId) {
                $query->where('ubicacion_id', $locationId);
            }])
            ->get()
            ->map(function ($product) {
                $defaultZoneStock = $product->zonasStock->firstWhere('pivot.es_zona_predeterminada_pos');

                if ($defaultZoneStock && $defaultZoneStock->pivot->stock > 0) {
                    $product->stock_display = $defaultZoneStock->pivot->stock;
                    $product->zone_display_id = $defaultZoneStock->id;
                    $product->zone_display_name = $defaultZoneStock->nombre;
                } elseif ($product->zonasStock->where('pivot.stock', '>', 0)->isNotEmpty()) {
                    $firstZoneWithStock = $product->zonasStock->where('pivot.stock', '>', 0)->first();
                    $product->stock_display = $firstZoneWithStock->pivot->stock;
                    $product->zone_display_id = $firstZoneWithStock->id;
                    $product->zone_display_name = $firstZoneWithStock->nombre;
                } else {
                    $product->stock_display = 0;
                    $product->zone_display_id = null;
                    $product->zone_display_name = 'Sin stock';
                }
                return $product;
            })
            ->filter(function ($product) {
                return $product->stock_display > 0;
            });
    }

    public function addProductToCart($productId, $zoneId)
    {
        $quantity = $this->quantities[$productId] ?? 1;

        if (!is_numeric($quantity) || $quantity <= 0) {
            $this->dispatch('app-notification-error', ['message' => 'La cantidad debe ser un número positivo.']);
            return;
        }

        $product = Producto::find($productId);
        if (!$product) {
            $this->dispatch('app-notification-error', ['message' => 'Producto no encontrado.']);
            return;
        }

        $stockEntry = $product->ubicaciones()->where('zona_id', $zoneId)->first();

        if (!$stockEntry || $stockEntry->pivot->stock < $quantity) {
            $this->dispatch('app-notification-error', ['message' => 'Stock insuficiente en la zona seleccionada.']);
            return;
        }

        $this->dispatch('productAddedToCart', productId: $productId, quantity: $quantity, zoneId: $zoneId);
        
        $this->searchTerm = '';
        if(isset($this->quantities[$productId])) {
            $this->quantities[$productId] = 1;
        }
    }

    public function render()
    {
        return view('livewire.pos-product-manager');
    }
}