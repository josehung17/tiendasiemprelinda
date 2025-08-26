<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Cliente;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PosMain extends Component
{
    public $selectedClient = null;
    public $cartItems = [];
    public $showProductDetailModal = false;
    public $selectedProductIdForDetail;
    public $bolivarExchangeRate = null; // To store the fetched exchange rate
    public $showPricesInBolivar = false; // To toggle currency display

    public function mount()
    {
        $this->fetchBolivarExchangeRate();
    }

    public function fetchBolivarExchangeRate()
    {
        try {
            $response = Http::get('https://bcv-api.rafnixg.dev/rates/');
            if ($response->successful()) {
                $data = $response->json();
                // Correctly access the 'dollar' key from the API response
                if (isset($data['dollar'])) {
                    $this->bolivarExchangeRate = (float) $data['dollar'];
                    $this->dispatch('success', 'Tipo de cambio del Bolívar actualizado.');
                } else {
                    // Log the unexpected format for debugging
                    \Log::error('BCV API: Unexpected data format (missing "dollar" key)', ['response_data' => $data]);
                    $this->dispatch('error', 'No se pudo obtener el tipo de cambio del Bolívar (formato inesperado). Por favor, intente de nuevo.');
                }
            } else {
                // Log the unsuccessful response for debugging
                \Log::error('BCV API: Unsuccessful response', ['status' => $response->status(), 'body' => $response->body()]);
                $this->dispatch('error', 'Error al obtener el tipo de cambio del Bolívar: ' . $response->status() . '. Por favor, intente de nuevo.');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('BCV API: Connection error', ['exception' => $e->getMessage()]);
            $this->dispatch('error', 'Error de conexión al obtener el tipo de cambio del Bolívar. Por favor, verifique su conexión a internet.');
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
    public function addProductToCart($productId, $quantity)
    {
        $product = Producto::find($productId);

        if (!$product) {
            $this->dispatch('error', 'Producto no encontrado.');
            return;
        }

        // Check if product already in cart
        foreach ($this->cartItems as $index => $item) {
            if ($item['product_id'] == $productId) {
                // Update quantity if already in cart
                $newQuantity = $item['quantity'] + $quantity;
                if ($product->stock < $newQuantity) {
                    $this->dispatch('error', 'Stock insuficiente para ' . $product->nombre . '. Stock disponible: ' . $product->stock);
                    return;
                }
                $this->cartItems[$index]['quantity'] = $newQuantity;
                $this->cartItems[$index]['subtotal'] = $newQuantity * $product->precio;
                $this->dispatch('success', 'Cantidad de ' . $product->nombre . ' actualizada en el carrito.');
                return;
            }
        }

        // Add new product to cart
        if ($product->stock < $quantity) {
            $this->dispatch('error', 'Stock insuficiente para ' . $product->nombre . '. Stock disponible: ' . $product->stock);
            return;
        }

        $this->cartItems[] = [
            'product_id' => $product->id,
            'nombre' => $product->nombre,
            'precio' => $product->precio,
            'quantity' => $quantity,
            'subtotal' => $product->precio * $quantity,
            'ruta_imagen' => $product->ruta_imagen, // Add product image path
        ];

        $this->dispatch('success', $product->nombre . ' agregado al carrito.');
    }

    public function removeCartItem($index)
    {
        if (isset($this->cartItems[$index])) {
            $productName = $this->cartItems[$index]['nombre'];
            unset($this->cartItems[$index]);
            $this->cartItems = array_values($this->cartItems); // Re-index the array
            $this->dispatch('success', $productName . ' eliminado del carrito.');
        }
    }

    public function updateCartItemQuantity($index, $quantity)
    {
        if (isset($this->cartItems[$index])) {
            $product = Producto::find($this->cartItems[$index]['product_id']);
            if (!$product) {
                $this->dispatch('error', 'Producto no encontrado.');
                return;
            }

            if (!is_numeric($quantity) || $quantity <= 0) {
                $this->dispatch('error', 'La cantidad debe ser un número positivo.');
                return;
            }

            if ($product->stock < $quantity) {
                $this->dispatch('error', 'Stock insuficiente para ' . $product->nombre . '. Stock disponible: ' . $product->stock);
                return;
            }

            $this->cartItems[$index]['quantity'] = $quantity;
            $this->cartItems[$index]['subtotal'] = $quantity * $product->precio;
            $this->dispatch('success', 'Cantidad de ' . $product->nombre . ' actualizada.');
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
