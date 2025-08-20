<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Livewire\WithPagination;

class GestionarProductos extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $productos = Producto::with(['categoria', 'marca'])
            ->where('nombre', 'like', '%'.$this->search.'%')
            ->orWhereHas('categoria', function ($query) {
                $query->where('nombre', 'like', '%'.$this->search.'%');
            })
            ->orWhereHas('marca', function ($query) {
                $query->where('nombre', 'like', '%'.$this->search.'%');
            })
            ->paginate(10);

        return view('livewire.gestionar-productos', [
            'productos' => $productos,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}