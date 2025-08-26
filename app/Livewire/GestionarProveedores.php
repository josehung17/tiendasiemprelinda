<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proveedor;
use Livewire\WithPagination;

class GestionarProveedores extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $proveedores = Proveedor::where('nombre', 'like', '%'.$this->search.'%')
            ->orWhere('email', 'like', '%'.$this->search.'%')
            ->orWhere('telefono', 'like', '%'.$this->search.'%')
            ->paginate(5);

        return view('livewire.gestionar-proveedores', [
            'proveedores' => $proveedores,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}