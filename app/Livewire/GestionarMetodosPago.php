<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MetodoPago;
use Livewire\WithPagination;

class GestionarMetodosPago extends Component
{
    use WithPagination;

    public $search = '';
    public $metodoPagoToDeleteId = null;

    protected $listeners = ['metodoPagoSaved' => 'render', 'confirmDelete' => 'confirmDelete'];

    public function render()
    {
        $metodosPago = MetodoPago::where('nombre', 'like', '%'.$this->search.'%')
            ->paginate(5);

        return view('livewire.gestionar-metodos-pago', [
            'metodosPago' => $metodosPago,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteMetodoPago($id)
    {
        $this->metodoPagoToDeleteId = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function confirmDelete()
    {
        if ($this->metodoPagoToDeleteId) {
            MetodoPago::find($this->metodoPagoToDeleteId)->delete();
            $this->dispatch('app-notification-success', message: 'Método de pago eliminado exitosamente.');
            $this->metodoPagoToDeleteId = null;
        }
    }
}
