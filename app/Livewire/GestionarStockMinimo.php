<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ubicacion;
use App\Models\Producto;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GestionarStockMinimo extends Component
{
    public $ubicaciones;
    public $selectedUbicacionId;
    public $showModal = false;
    public $productoSeleccionado;

    protected $listeners = ['stockMinimoActualizado' => '$refresh'];

    public function mount()
    {
        $this->ubicaciones = Ubicacion::all();
    }

    public function abrirModalStockMinimo($productoId)
    {
        $this->productoSeleccionado = $productoId;
        $this->dispatch('abrirStockMinimoModal', productoId: $productoId, ubicacionId: $this->selectedUbicacionId);
    }

    public function render()
    {
        $productos = collect(); // Colección vacía por defecto

        if ($this->selectedUbicacionId) {
            $productos = Producto::whereHas('ubicaciones', function ($query) {
                $query->where('ubicacion_id', $this->selectedUbicacionId);
            })->with([
                'ubicaciones' => function ($query) {
                    $query->where('ubicacion_id', $this->selectedUbicacionId);
                },
                ])->get();
        }

        return view('livewire.gestionar-stock-minimo', [
            'productos' => $productos,
        ]);
    }
}
