<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Marca;
use App\Models\Categoria;

class GestionarCreacionProducto extends Component
{
    public $marcas;
    public $categorias;
    public $marca_id;

    protected $listeners = ['marcaCreada' => 'handleMarcaCreada'];

    public function mount($marcas, $categorias)
    {
        $this->marcas = $marcas;
        $this->categorias = $categorias;
    }

    public function handleMarcaCreada($marcaId)
    {
        $this->marcas = Marca::all();
        $this->marca_id = $marcaId;
    }

    public function openCrearMarcaModal()
    {
        $this->dispatch('openCrearMarcaModal');
    }

    public function render()
    {
        return view('livewire.gestionar-creacion-producto');
    }
}
