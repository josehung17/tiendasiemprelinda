<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto; // Assuming you'll need the Product model

class ConfirmPurchasePriceUpdate extends Component
{
    public $productId;
    public $currentPurchasePrice;
    public $newPurchasePrice;
    public $showModal = false;

    protected $listeners = ['openConfirmPurchasePriceModal' => 'openModal'];

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function confirmUpdate()
    {
        $this->dispatch('purchasePriceUpdateConfirmed', $this->productId, $this->newPurchasePrice);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.confirm-purchase-price-update');
    }

    public function openModal($productId, $currentPurchasePrice, $newPurchasePrice)
    {
        $this->productId = $productId;
        $this->currentPurchasePrice = $currentPurchasePrice;
        $this->newPurchasePrice = $newPurchasePrice;
        $this->showModal = true;
    }
}