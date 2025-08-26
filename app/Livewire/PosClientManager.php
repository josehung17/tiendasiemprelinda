<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use Livewire\Attributes\On;

class PosClientManager extends Component
{
    public $searchTerm = '';
    public $selectedClient = null;
    public $showCreateClientModal = false;
    public function getSearchResultsProperty()
    {
        if (empty($this->searchTerm)) {
            return collect(); // Return an empty collection
        }

        return Cliente::where('numero_documento', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('nombre', 'like', '%' . $this->searchTerm . '%')
                        ->limit(10)
                        ->get();
    }

    public function selectClient($clientId)
    {
        $this->selectedClient = Cliente::find($clientId);
        $this->searchTerm = ''; // Clear search term
        $this->searchResults = []; // Clear search results
    }

    public function openCreateClientModal()
    {
        $this->showCreateClientModal = true;
    }

    #[On('clientCreated')]
    public function clientCreated($clientId)
    {
        $this->selectedClient = Cliente::find($clientId);
        $this->showCreateClientModal = false; // Close the modal
        $this->searchTerm = ''; // Clear search term
        $this->searchResults = []; // Clear search results
    }

    public function render()
    {
        return view('livewire.pos-client-manager');
    }
}
