<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Livewire\Attributes\On;

class ProductModal extends Component
{
    public ?Producto $product = null;
    public $show = false;

    public function mount($productId)
    {
        $this->product = Producto::with(['categoria', 'marca'])->find($productId);
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
