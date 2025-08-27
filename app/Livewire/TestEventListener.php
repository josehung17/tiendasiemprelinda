<?php

namespace App\Livewire;

use Livewire\Component;

class TestEventListener extends Component
{
    protected $listeners = ['openConfirmPurchasePriceModal' => 'handleEvent'];

    public function handleEvent($productId, $currentPurchasePrice, $newPurchasePrice)
    {
        // Event handled
    }

    public function render()
    {
        return view('livewire.test-event-listener');
    }
}