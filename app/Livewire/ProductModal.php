<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class ProductModal extends Component
{
    public ?Producto $product = null;
    public $show = false;
    public $productId;
    public $stockLocations = [];

    public function mount($productId)
    {
        $this->productId = $productId; // Assign to public property
        $this->loadProduct();
    }

    public function updatedProductId($value)
    {
        $this->loadProduct();
    }

    private function loadProduct()
    {
        $this->product = Producto::with(['categoria', 'marca'])->find($this->productId);

        if ($this->product) {
            $this->stockLocations = DB::table('producto_ubicacion')
                ->join('ubicaciones', 'producto_ubicacion.ubicacion_id', '=', 'ubicaciones.id')
                ->leftJoin('zonas', 'producto_ubicacion.zona_id', '=', 'zonas.id')
                ->where('producto_ubicacion.producto_id', $this->productId)
                ->where('producto_ubicacion.stock', '>', 0)
                ->select(
                    'ubicaciones.nombre as ubicacion_nombre',
                    'zonas.nombre as zona_nombre',
                    'producto_ubicacion.stock'
                )
                ->orderBy('ubicaciones.nombre')
                ->orderBy('zonas.nombre')
                ->get();
        }

        $this->show = true;
    }

    public function updatedShow($value)
    {
        if ($value === false) {
            $this->dispatch('forceCloseProductModal');
        }
    }

    public function closeModal()
    {
        $this->show = false;
        $this->dispatch('forceCloseProductModal');
    }

    public function render()
    {
        return view('livewire.product-modal');
    }
}
