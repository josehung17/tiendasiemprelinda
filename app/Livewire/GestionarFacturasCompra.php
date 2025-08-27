<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\FacturaCompra;
use Livewire\WithPagination;

class GestionarFacturasCompra extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['facturaCompraSaved' => 'render'];

    public function render()
    {
        $facturas = FacturaCompra::with(['proveedor', 'user'])
            ->whereHas('proveedor', function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('fecha_factura', 'like', '%' . $this->search . '%')
            ->orderBy('fecha_factura', 'desc')
            ->paginate(10);

        return view('livewire.gestionar-facturas-compra', [
            'facturas' => $facturas,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}