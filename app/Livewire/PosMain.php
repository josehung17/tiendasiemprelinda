<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Zona;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TasaDeCambio;
use App\Models\Ubicacion;

class PosMain extends Component
{
    public $selectedClient = null;
    public $cartItems = [];
    public $showProductDetailModal = false;
    public $selectedProductIdForDetail;
    public $bolivarExchangeRate = null;
    public $showPricesInBolivar = false;

    public $locationIsSelected = false;
    public $availableLocations = [];
    public $selectedLocationToSet;
    public $activeLocationId;
    public $activeLocationName;

    public function mount()
    {
        $this->availableLocations = Ubicacion::where('tipo', 'tienda')->orderBy('nombre')->get();

        if (session()->has('pos_location_id')) {
            $this->activeLocationId = session('pos_location_id');
            $location = Ubicacion::find($this->activeLocationId);
            if ($location) {
                $this->activeLocationName = $location->nombre;
                $this->locationIsSelected = true;
                $this->fetchBolivarExchangeRateFromDatabase();
            } else {
                session()->forget('pos_location_id');
                $this->locationIsSelected = false;
            }
        } else {
            $this->locationIsSelected = false;
        }
    }

    public function setLocation()
    {
        $this->validate(['selectedLocationToSet' => 'required|exists:ubicaciones,id']);

        session(['pos_location_id' => $this->selectedLocationToSet]);
        $this->activeLocationId = $this->selectedLocationToSet;
        $this->activeLocationName = Ubicacion::find($this->activeLocationId)->nombre;
        $this->locationIsSelected = true;
        $this->cartItems = []; // Clear cart when changing location

        $this->fetchBolivarExchangeRateFromDatabase();
    }

    public function clearLocation()
    {
        session()->forget('pos_location_id');
        $this->locationIsSelected = false;
        $this->activeLocationId = null;
        $this->activeLocationName = null;
        $this->cartItems = [];
    }

    public function fetchBolivarExchangeRateFromDatabase()
    {
        try {
            $tasa = TasaDeCambio::where('moneda', 'USD')->latest('fecha_actualizacion')->first();

            if ($tasa) {
                $this->bolivarExchangeRate = (float) $tasa->tasa;
                $this->dispatch('app-notification-success', ['message' => 'Tasa de cambio actualizada.']);
            } else {
                $this->dispatch('app-notification-error', ['message' => 'No se encontró tasa de cambio para USD.']);
                $this->bolivarExchangeRate = null;
            }
        } catch (\Exception $e) {
            Log::error('Error al cargar la tasa de cambio: ' . $e->getMessage());
            $this->dispatch('app-notification-error', ['message' => 'Error al cargar la tasa de cambio.']);
            $this->bolivarExchangeRate = null;
        }
    }

    public function toggleCurrencyDisplay()
    {
        $this->showPricesInBolivar = !$this->showPricesInBolivar;
    }

    #[On('productModalClosed')]
    public function handleProductModalClosed()
    {
        $this->showProductDetailModal = false;
        $this->selectedProductIdForDetail = null;
    }

    public function openProductDetailModal($productId)
    {
        $this->selectedProductIdForDetail = $productId;
        $this->showProductDetailModal = true;
    }

    #[On('clientSelected')]
    public function clientSelected($clientId)
    {
        $this->selectedClient = Cliente::find($clientId);
    }

    #[On('productAddedToCart')]
    public function addProductToCart($productId, $quantity, $zoneId)
    {
        $product = Producto::find($productId);
        if (!$product) {
            $this->dispatch('app-notification-error', ['message' => 'Producto no encontrado.']);
            return;
        }

        $stockEntry = $product->ubicaciones()
            ->where('ubicacion_id', $this->activeLocationId)
            ->where('zona_id', $zoneId)
            ->first();

        if (!$stockEntry) {
            $this->dispatch('app-notification-error', ['message' => 'Error de stock. No se encontró el producto en la zona especificada.']);
            return;
        }
        
        $zoneName = Zona::find($zoneId)->nombre ?? 'N/A';

        $cartIndex = null;
        foreach ($this->cartItems as $index => $item) {
            if ($item['product_id'] == $productId && $item['zone_id'] == $zoneId) {
                $cartIndex = $index;
                break;
            }
        }

        if ($cartIndex !== null) {
            $newQuantity = $this->cartItems[$cartIndex]['quantity'] + $quantity;
            if ($stockEntry->pivot->stock < $newQuantity) {
                $this->dispatch('app-notification-error', ['message' => 'Stock insuficiente para ' . $product->nombre . ' en ' . $zoneName]);
                return;
            }
            $this->cartItems[$cartIndex]['quantity'] = $newQuantity;
            $this->cartItems[$cartIndex]['subtotal'] = $newQuantity * $product->precio;
        } else {
            if ($stockEntry->pivot->stock < $quantity) {
                $this->dispatch('app-notification-error', ['message' => 'Stock insuficiente para ' . $product->nombre . ' en ' . $zoneName]);
                return;
            }

            $this->cartItems[] = [
                'product_id' => $product->id,
                'nombre' => $product->nombre,
                'precio' => $product->precio,
                'quantity' => $quantity,
                'subtotal' => $product->precio * $quantity,
                'ruta_imagen' => $product->ruta_imagen,
                'zone_id' => $zoneId,
                'zone_name' => $zoneName,
                'stock_disponible' => $stockEntry->pivot->stock,
            ];
        }

        $this->dispatch('app-notification-success', ['message' => $product->nombre . ' agregado desde ' . $zoneName]);
    }

    public function removeCartItem($index)
    {
        if (isset($this->cartItems[$index])) {
            $productName = $this->cartItems[$index]['nombre'];
            unset($this->cartItems[$index]);
            $this->cartItems = array_values($this->cartItems);
            $this->dispatch('app-notification-success', ['message' => $productName . ' eliminado del carrito.']);
        }
    }

    public function updateCartItemQuantity($index, $quantity)
    {
        if (isset($this->cartItems[$index])) {
            $item = $this->cartItems[$index];
            
            if (!is_numeric($quantity) || $quantity <= 0) {
                $this->removeCartItem($index);
                return;
            }

            $stockEntry = Producto::find($item['product_id'])->ubicaciones()
                ->where('ubicacion_id', $this->activeLocationId)
                ->where('zona_id', $item['zone_id'])
                ->first();

            if (!$stockEntry || $stockEntry->pivot->stock < $quantity) {
                $this->dispatch('app-notification-error', ['message' => 'Stock insuficiente. Disponible: ' . ($stockEntry->pivot->stock ?? 0)]);
                $this->cartItems[$index]['quantity'] = $stockEntry->pivot->stock > 0 ? 1 : 0; // Revert to 1 or 0
                return;
            }

            $this->cartItems[$index]['quantity'] = $quantity;
            $this->cartItems[$index]['subtotal'] = $quantity * $item['precio'];
            $this->dispatch('app-notification-success', ['message' => 'Cantidad actualizada.']);
        }
    }

    public function getTotalProperty()
    {
        return array_sum(array_column($this->cartItems, 'subtotal'));
    }

    public function getBolivarTotalProperty()
    {
        if ($this->bolivarExchangeRate === null) {
            return null;
        }
        return $this->total * $this->bolivarExchangeRate;
    }

    public function render()
    {
        return view('livewire.pos-main');
    }
}
