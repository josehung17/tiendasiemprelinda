<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Marca;

class CrearMarcaModal extends Component
{
    public $show = false;
    public $nombre;

    protected $listeners = ['openCrearMarcaModal'];

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:marcas,nombre',
    ];

    public function openCrearMarcaModal()
    {
        $this->show = true;
    }

    public function store()
    {
        $this->validate();

        $marca = Marca::create([
            'nombre' => $this->nombre,
        ]);

        $this->reset('nombre');
        $this->show = false;

        $this->dispatch('marcaCreada', $marca->id);
    }

    public function render()
    {
        return view('livewire.crear-marca-modal');
    }
}
