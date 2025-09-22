<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MetodoPago;
use App\Models\Cuenta;
use Livewire\WithPagination;

class GestionarMetodosPago extends Component
{
    use WithPagination;

    public $showModal = false;
    public $metodoPagoId;
    public $nombre, $cuenta_id;
    public $allCuentas = [];
    public $search = '';

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'cuenta_id' => 'required|exists:cuentas,id',
    ];

    public function mount()
    {
        $this->allCuentas = Cuenta::with('moneda')->orderBy('nombre')->get();
    }

    public function render()
    {
        $metodosPago = MetodoPago::with('cuenta.moneda')
                            ->where('nombre', 'like', '%'.$this->search.'%')
                            ->paginate(10);

        return view('livewire.gestionar-metodos-pago', [
            'metodosPago' => $metodosPago,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $metodoPago = MetodoPago::findOrFail($id);
        $this->metodoPagoId = $id;
        $this->nombre = $metodoPago->nombre;
        $this->cuenta_id = $metodoPago->cuenta_id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        MetodoPago::updateOrCreate(['id' => $this->metodoPagoId], [
            'nombre' => $this->nombre,
            'cuenta_id' => $this->cuenta_id,
        ]);

        $this->dispatch('app-notification-success', message: 'Método de pago guardado correctamente.');
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function delete($id)
    {
        MetodoPago::find($id)->delete();
        $this->dispatch('app-notification-success', message: 'Método de pago eliminado correctamente.');
    }

    private function resetInputFields()
    {
        $this->metodoPagoId = null;
        $this->nombre = '';
        $this->cuenta_id = '';
    }
}