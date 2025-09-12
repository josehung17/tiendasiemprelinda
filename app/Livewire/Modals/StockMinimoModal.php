<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Ubicacion;
use Livewire\Attributes\On;

class StockMinimoModal extends Component
{
    public $show = false;
    public $producto;
    public $ubicacionId;
    public $stocksMinimos = [];
    public $zonas = [];

    #[On('abrirStockMinimoModal')]
    public function abrirModal($productoId, $ubicacionId)
    {
        $this->producto = Producto::find($productoId);
        $this->ubicacionId = $ubicacionId;

        // Usar la nueva relación 'zonas' y filtrar por la ubicación correcta desde el pivote
        $this->zonas = $this->producto->zonas()->wherePivot('ubicacion_id', $this->ubicacionId)->get();

        $this->stocksMinimos = [];
        foreach ($this->zonas as $zona) {
            $this->stocksMinimos[$zona->id] = $zona->pivot->stock_minimo;
        }

        $this->show = true;
    }

    public function save()
    {
        if (!$this->producto) {
            return;
        }

        foreach ($this->stocksMinimos as $zonaId => $minimo) {
            $this->producto->zonas()
                ->wherePivot('ubicacion_id', $this->ubicacionId)
                ->updateExistingPivot($zonaId, ['stock_minimo' => $minimo ?? 0]);
        }

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Stock mínimo actualizado exitosamente.']);
        $this->cerrarModal();
        $this->dispatch('stockMinimoActualizado'); // Emitir evento para el padre
    }

    public function cerrarModal()
    {
        $this->show = false;
        $this->reset(['producto', 'ubicacionId', 'stocksMinimos', 'zonas']);
    }

    public function render()
    {
        return view('livewire.modals.stock-minimo-modal');
    }
}
