<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use Livewire\WithPagination;

class GestionarClientes extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $clientes = Cliente::where('nombre', 'like', '%'.$this->search.'%')
            ->orWhere('numero_documento', 'like', '%'.$this->search.'%')
            ->orWhere('email', 'like', '%'.$this->search.'%')
            ->paginate(10);

        return view('livewire.gestionar-clientes', [
            'clientes' => $clientes,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}