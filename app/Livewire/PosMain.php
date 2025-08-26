<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Cliente;
use Livewire\Attributes\On;

class PosMain extends Component
{
    public $selectedClient = null;
    public $cartItems = [];
    public $showProductDetailModal = false;
    public $selectedProductIdForDetail;

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

    public function render()
    {
        return view('livewire.pos-main');
    }
}
