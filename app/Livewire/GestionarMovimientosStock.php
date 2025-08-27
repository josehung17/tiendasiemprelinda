<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\MovimientoStock;
use Livewire\WithPagination;
use Livewire\Attributes\Layout; // Import the Layout attribute
use Livewire\Attributes\On; // Import the On attribute

#[Layout('layouts.app')] // Specify the layout for this page component
class GestionarMovimientosStock extends Component
{
    use WithPagination;

    public $proveedores;

    public $searchTerm = ''; // For product search autocomplete
    public $selectedProductId;
    public $selectedProductName = ''; // To display the selected product's name

    public $cantidad;
    public $tipoMovimiento = 'entrada-reposicion'; // Default type
    public $precioCompraUnitario;
    public $selectedProveedorId;
    public $referenciaVenta;
    public $motivoAjuste;

    public $currentProductPurchasePrice; // New property to store current product purchase price

    protected $rules = [
        'selectedProductId' => 'required|exists:productos,id',
        'selectedProductName' => 'required|string', // Ensure a product is selected
        'cantidad' => 'required|integer|min:1',
        'tipoMovimiento' => 'required|in:entrada-reposicion,salida-venta,ajuste-manual',
        'precioCompraUnitario' => 'nullable|numeric|min:0',
        'selectedProveedorId' => 'nullable|exists:proveedores,id',
        'referenciaVenta' => 'nullable|string|max:255',
        'motivoAjuste' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->proveedores = Proveedor::all();
        // If a product is already selected (e.g., from a previous session or URL param)
        if ($this->selectedProductId) {
            $product = Producto::find($this->selectedProductId);
            if ($product) {
                $this->selectedProductName = $product->nombre;
            }
        }
        // Ensure precioCompraUnitario is null on initial load unless explicitly set
        $this->precioCompraUnitario = null;
    }

    public function getProductSearchResultsProperty()
    {
        if (empty($this->searchTerm)) {
            return collect();
        }

        return Producto::where('nombre', 'like', '%' . $this->searchTerm . '%')
                        ->limit(10)
                        ->get();
    }

    public function selectProduct($productId, $productName)
    {
        $this->selectedProductId = $productId;
        $this->selectedProductName = $productName;
        $this->searchTerm = ''; // Clear search term after selection

        $product = Producto::find($productId);
        if ($product) {
            $this->currentProductPurchasePrice = $product->precio_compra;
        }
    }

    public function realizarMovimiento()
    {
        $this->validate();

        $producto = Producto::find($this->selectedProductId);

        // Check if it's an 'entrada-reposicion' and a new purchase price is provided
        if ($this->tipoMovimiento === 'entrada-reposicion' && $this->precioCompraUnitario !== null) {
            // Compare with current product purchase price
            if ($producto->precio_compra != $this->precioCompraUnitario) {
                 // Prices are different, open confirmation modal
                $this->dispatch('openConfirmPurchasePriceModal',
                    $this->selectedProductId,
                    $producto->precio_compra,
                    $this->precioCompraUnitario
                );
                return; // Stop execution here, wait for modal confirmation
            }
        }

        // If prices are the same or not applicable, proceed with stock movement
        $this->processStockMovement($producto);
    }

    #[On('purchasePriceUpdateConfirmed')]
    public function updateProductPurchasePrice($productId, $newPurchasePrice)
    {
        $producto = Producto::find($productId);
        if ($producto) {
            $producto->precio_compra = $newPurchasePrice;
            $producto->save();
        }

        // Now proceed with the stock movement
        $this->processStockMovement($producto);
    }

    private function processStockMovement(Producto $producto)
    {
        // Determine the actual quantity change
        $cantidadCambio = $this->cantidad;
        if ($this->tipoMovimiento === 'salida-venta' || $this->tipoMovimiento === 'ajuste-manual') {
            // For sales or manual adjustments, quantity should be subtracted
            $cantidadCambio = -$this->cantidad;
        }

        // Update product stock
        $producto->stock += $cantidadCambio;
        $producto->save();

        // Create stock movement record
        MovimientoStock::create([
            'producto_id' => $this->selectedProductId,
            'tipo' => $this->tipoMovimiento,
            'cantidad' => $this->cantidad, // Always store positive quantity in movement record
            'precio_compra_unitario' => $this->precioCompraUnitario,
            'proveedor_id' => $this->selectedProveedorId,
            'referencia_venta' => $this->referenciaVenta,
            'motivo_ajuste' => $this->motivoAjuste,
        ]);

        // Reset form fields
        $this->reset([
            'searchTerm', // Reset search term
            'selectedProductId',
            'selectedProductName', // Reset selected product name
            'cantidad',
            'precioCompraUnitario',
            'selectedProveedorId',
            'referenciaVenta',
            'motivoAjuste',
        ]);

        session()->flash('message', 'Movimiento de stock realizado exitosamente.');
    }

    public function render()
    {
        $movimientos = MovimientoStock::with(['producto', 'proveedor'])
            ->latest()
            ->paginate(10);

        return view('livewire.gestionar-movimientos-stock', [
            'movimientos' => $movimientos,
        ]);
    }
}