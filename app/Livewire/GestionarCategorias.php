<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Categoria;
use Livewire\WithPagination;

class GestionarCategorias extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $categorias = Categoria::where('nombre', 'like', '%'.$this->search.'%')
            ->paginate(5); // Paginar de 5 en 5

        return view('livewire.gestionar-categorias', [
            'categorias' => $categorias,
        ]);
    }

    // Este método se asegura de que la paginación se reinicie cuando buscas algo
    public function updatingSearch()
    {
        $this->resetPage();
    }
}