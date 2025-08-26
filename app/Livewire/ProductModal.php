<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Livewire\Attributes\On;

class ProductModal extends Component
{
    public ?Producto $product = null;
    public $show = false;
    public $productId; // Declare productId as a public property

    public function mount($productId)
    {
        $this->productId = $productId; // Assign to public property
        $this->loadProduct();
    }

    public function updatedProductId($value)
    {
        $this->loadProduct();
    }

    private function loadProduct()
    {
        $this->product = Producto::with(['categoria', 'marca'])->find($this->productId);
        $this->show = true;
    }

    public function closeModal()
    {
        $this->show = false;
        $this->dispatch('productModalClosed');
    }

    public function render()
    {
        return view('livewire.product-modal');
    }
}
