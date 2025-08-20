<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Marca;
use Livewire\WithPagination;

class GestionarMarcas extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $marcas = Marca::where('nombre', 'like', '%'.$this->search.'%')
            ->paginate(5);

        return view('livewire.gestionar-marcas', [
            'marcas' => $marcas,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}