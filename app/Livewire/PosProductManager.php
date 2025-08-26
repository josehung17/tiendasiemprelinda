<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto; // Assuming your product model is named Producto

class PosProductManager extends Component
{
    public $searchTerm = '';
    public $quantities = []; // To store quantities for each product in search results

    public function getSearchResultsProperty()
    {
        if (empty($this->searchTerm)) {
            return collect();
        }

        return Producto::where('nombre', 'like', '%' . $this->searchTerm . '%')
                        ->limit(10)
                        ->get();
    }

    public function addProductToCart($productId)
    {
        $quantity = $this->quantities[$productId] ?? 1; // Default to 1 if not set

        // Basic validation for quantity
        if (!is_numeric($quantity) || $quantity <= 0) {
            $this->dispatch('error', 'La cantidad debe ser un número positivo.');
            return;
        }

        $product = Producto::find($productId);

        if (!$product) {
            $this->dispatch('error', 'Producto no encontrado.');
            return;
        }

        // Check stock (simple example, you might have more complex stock management)
        if ($product->stock < $quantity) {
            $this->dispatch('error', 'Stock insuficiente para ' . $product->nombre . '. Stock disponible: ' . $product->stock);
            return;
        }

        $this->dispatch('productAddedToCart', $productId, $quantity);
        $this->searchTerm = ''; // Clear search term after adding
        $this->quantities[$productId] = 1; // Reset quantity for that product
    }

    public function render()
    {
        return view('livewire.pos-product-manager');
    }
}
