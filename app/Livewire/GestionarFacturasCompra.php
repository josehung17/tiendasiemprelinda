<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\FacturaCompra;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class GestionarFacturasCompra extends Component
{
    use WithPagination;

    public function mount()
    {
        if (session()->has('message')) {
            $this->dispatch('app-notification-success', message: session('message'));
        }
    }

    public $search = '';
    public $showVerFacturaModal = false;
    public $showDeleteModal = false;
    public ?FacturaCompra $facturaSeleccionada = null;
    public $facturaAEliminarId = null;

    protected $listeners = ['facturaCompraSaved' => 'render'];

    public function render()
    {
        $facturas = FacturaCompra::with(['proveedor', 'user', 'tasaDeCambio'])
            ->where(function ($query) {
                $query->where('id', 'like', '%' . $this->search . '%')
                      ->orWhere('fecha_factura', 'like', '%' . $this->search . '%')
                      ->orWhereHas('proveedor', function ($subQuery) {
                          $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('user', function ($subQuery) {
                          $subQuery->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.gestionar-facturas-compra', [
            'facturas' => $facturas,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function verFactura($facturaId)
    {
        $this->facturaSeleccionada = FacturaCompra::with(['proveedor', 'user', 'detalles.producto', 'pagos.metodoPago', 'tasaDeCambio'])->find($facturaId);
        $this->showVerFacturaModal = true;
    }

    public function confirmarEliminacion($facturaId)
    {
        $this->facturaAEliminarId = $facturaId;
        $this->showDeleteModal = true;
    }

    public function deleteFactura()
    {
        $factura = FacturaCompra::with('detalles.producto')->find($this->facturaAEliminarId);

        if ($factura) {
            DB::transaction(function () use ($factura) {
                foreach ($factura->detalles as $detalle) {
                    if ($detalle->producto && $detalle->ubicacion_id) { // Ensure ubicacion_id exists
                        DB::table('producto_ubicacion')->where([
                            ['producto_id', '=', $detalle->producto_id],
                            ['ubicacion_id', '=', $detalle->ubicacion_id],
                            ['zona_id', '=', $detalle->zona_id],
                        ])->decrement('stock', $detalle->cantidad);
                    }
                }
                $factura->delete();
            });

            $this->dispatch('app-notification-success', message: 'Factura eliminada correctamente.');
        } else {
            $this->dispatch('app-notification-error', message: 'No se pudo encontrar la factura.');
        }

        $this->showDeleteModal = false;
        $this->facturaAEliminarId = null;
        $this->render();
    }
}
