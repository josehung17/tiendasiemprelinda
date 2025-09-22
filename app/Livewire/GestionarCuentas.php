<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cuenta;
use App\Models\Moneda;
use Livewire\WithPagination;

class GestionarCuentas extends Component
{
    use WithPagination;

    public $showModal = false;
    public $cuentaId;
    public $nombre, $datos_adicionales, $moneda_id;
    public $allMonedas = [];
    public $search = '';

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'moneda_id' => 'required|exists:monedas,id',
        'datos_adicionales' => 'nullable|string',
    ];

    public function mount()
    {
        $this->allMonedas = Moneda::orderBy('nombre')->get();
    }

    public function render()
    {
        $cuentas = Cuenta::with('moneda')
                        ->where('nombre', 'like', '%'.$this->search.'%')
                        ->paginate(10);

        return view('livewire.gestionar-cuentas', [
            'cuentas' => $cuentas,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $cuenta = Cuenta::findOrFail($id);
        $this->cuentaId = $id;
        $this->nombre = $cuenta->nombre;
        $this->datos_adicionales = $cuenta->datos_adicionales;
        $this->moneda_id = $cuenta->moneda_id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Cuenta::updateOrCreate(['id' => $this->cuentaId], [
            'nombre' => $this->nombre,
            'datos_adicionales' => $this->datos_adicionales,
            'moneda_id' => $this->moneda_id,
        ]);

        $this->dispatch('app-notification-success', message: 'Cuenta guardada correctamente.');
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function delete($id)
    {
        Cuenta::find($id)->delete();
        $this->dispatch('app-notification-success', message: 'Cuenta eliminada correctamente.');
    }

    private function resetInputFields()
    {
        $this->cuentaId = null;
        $this->nombre = '';
        $this->datos_adicionales = '';
        $this->moneda_id = '';
    }
}