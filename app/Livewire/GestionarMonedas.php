<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Moneda;
use Livewire\WithPagination;

class GestionarMonedas extends Component
{
    use WithPagination;

    public $showModal = false;
    public $monedaId;
    public $nombre, $simbolo, $codigo;
    public $search = '';

    protected $rules = [
        'nombre' => 'required|string|max:50',
        'simbolo' => 'required|string|max:5',
        'codigo' => 'required|string|max:3|unique:monedas,codigo',
    ];

    public function render()
    {
        $monedas = Moneda::where('nombre', 'like', '%'.$this->search.'%')
                         ->orWhere('codigo', 'like', '%'.$this->search.'%')
                         ->paginate(10);

        return view('livewire.gestionar-monedas', [
            'monedas' => $monedas,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $moneda = Moneda::findOrFail($id);
        $this->monedaId = $id;
        $this->nombre = $moneda->nombre;
        $this->simbolo = $moneda->simbolo;
        $this->codigo = $moneda->codigo;
        $this->showModal = true;
    }

    public function save()
    {
        // Adjust validation rule for updates
        if ($this->monedaId) {
            $this->rules['codigo'] = 'required|string|max:3|unique:monedas,codigo,' . $this->monedaId;
        }

        $this->validate();

        Moneda::updateOrCreate(['id' => $this->monedaId], [
            'nombre' => $this->nombre,
            'simbolo' => $this->simbolo,
            'codigo' => $this->codigo,
        ]);

        $this->dispatch('app-notification-success', message: 'Moneda guardada correctamente.');
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function delete($id)
    {
        Moneda::find($id)->delete();
        $this->dispatch('app-notification-success', message: 'Moneda eliminada correctamente.');
    }

    private function resetInputFields()
    {
        $this->monedaId = null;
        $this->nombre = '';
        $this->simbolo = '';
        $this->codigo = '';
    }
}