<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\MovimientoStock;
use Livewire\WithPagination;
use Livewire\Attributes\Layout; // Import the Layout attribute
use App\Models\Ubicacion;
use App\Models\Zona;
use Livewire\Attributes\On; // Import the On attribute

#[Layout('layouts.app')] // Specify the layout for this page component
class GestionarMovimientosStock extends Component
{
    use WithPagination;

    public $proveedores;
    public $ubicaciones;

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

    // Propiedades para movimientos entre ubicaciones/zonas
    public $ubicacionOrigenId;
    public $zonaOrigenId;
    public $ubicacionDestinoId;
    public $zonaDestinoId;
    public $zonasOrigen = [];
    public $zonasDestino = [];

    // Propiedades para ubicación/zona afectada en movimientos que no son transferencia
    public $ubicacionAfectadaId;
    public $zonaAfectadaId;
    public $zonasAfectadas = [];
    public $showSearchResults = false;
    public $stockExistenteOrigen = 0;
    public $stockExistenteDestino = 0;

    public function mount()
    {
        $this->proveedores = Proveedor::all();
        $this->ubicaciones = Ubicacion::all();

        // If a product is already selected (e.g., from a previous session or URL param)
        if ($this->selectedProductId) {
            $product = Producto::find($this->selectedProductId);
            if ($product) {
                $this->selectedProductName = $product->nombre;
            }
        }
        // Ensure precioCompraUnitario is null on initial load unless explicitly set
        $this->precioCompraUnitario = null;

        // Cargar zonas si las ubicaciones ya están seleccionadas (útil para recargas de página)
        if ($this->ubicacionOrigenId) {
            $this->updatedUbicacionOrigenId($this->ubicacionOrigenId);
        }
        if ($this->ubicacionDestinoId) {
            $this->updatedUbicacionDestinoId($this->ubicacionDestinoId);
        }
        if ($this->ubicacionAfectadaId) {
            $this->updatedUbicacionAfectadaId($this->ubicacionAfectadaId);
        }
        $this->actualizarStockExistenteOrigen(); // Actualizar stock al cargar el componente
        $this->actualizarStockExistenteDestino(); // Actualizar stock al cargar el componente
    }

    public function updatedUbicacionOrigenId($value)
    {
        $this->zonasOrigen = Zona::where('ubicacion_id', $value)->get();
        $this->zonaOrigenId = null; // Reset zona seleccionada
        $this->stockExistenteOrigen = 0; // Reset stock al cambiar ubicación
        $this->actualizarStockExistenteOrigen();
    }

    public function updatedZonaOrigenId($value)
    {
        $this->actualizarStockExistenteOrigen();
    }

    public function updatedUbicacionDestinoId($value)
    {
        $this->zonasDestino = Zona::where('ubicacion_id', $value)->get();
        $this->zonaDestinoId = null; // Reset zona seleccionada
        $this->stockExistenteDestino = 0; // Reset stock al cambiar ubicación
        $this->actualizarStockExistenteDestino();
    }

    public function updatedZonaDestinoId($value)
    {
        $this->actualizarStockExistenteDestino();
    }

    public function updatedUbicacionAfectadaId($value)
    {
        $this->zonasAfectadas = Zona::where('ubicacion_id', $value)->get();
        $this->zonaAfectadaId = null; // Reset zona seleccionada
    }

    private function actualizarStockExistenteOrigen()
    {
        if ($this->selectedProductId && $this->ubicacionOrigenId && $this->zonaOrigenId) {
            $producto = Producto::find($this->selectedProductId);
            $pivot = $producto->ubicaciones()
                              ->where('ubicacion_id', $this->ubicacionOrigenId)
                              ->where('zona_id', $this->zonaOrigenId)
                              ->first();
            $this->stockExistenteOrigen = $pivot ? $pivot->pivot->stock : 0;
        } else {
            $this->stockExistenteOrigen = 0;
        }
    }

    private function actualizarStockExistenteDestino()
    {
        if ($this->selectedProductId && $this->ubicacionDestinoId && $this->zonaDestinoId) {
            $producto = Producto::find($this->selectedProductId);
            $pivot = $producto->ubicaciones()
                              ->where('ubicacion_id', $this->ubicacionDestinoId)
                              ->where('zona_id', $this->zonaDestinoId)
                              ->first();
            $this->stockExistenteDestino = $pivot ? $pivot->pivot->stock : 0;
        } else {
            $this->stockExistenteDestino = 0;
        }
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

    public function updatedSearchTerm($value)
    {
        if (!empty($value)) {
            $this->showSearchResults = true;
        } else {
            $this->showSearchResults = false;
        }
    }

    public function selectProduct($productId, $productName)
    {
        $this->selectedProductId = $productId;
        $this->selectedProductName = $productName;
        $this->searchTerm = $productName; // Autocompletar el input de búsqueda
        $this->showSearchResults = false; // Ocultar resultados de búsqueda
        $this->stockExistenteOrigen = 0; // Reset stock existente al cambiar de producto

        $product = Producto::find($productId);
        if ($product) {
            $this->currentProductPurchasePrice = $product->precio_compra;
        }
        $this->actualizarStockExistenteOrigen(); // Actualizar stock al seleccionar producto
        $this->actualizarStockExistenteDestino(); // Actualizar stock al seleccionar producto
    }

    public function realizarMovimiento()
    {
        $rules = [
            'selectedProductId' => 'required|integer|exists:productos,id',
            'selectedProductName' => 'required|string',
            'cantidad' => 'required|integer|min:1',
            'tipoMovimiento' => 'required|string|in:transferencia,ajuste_entrada,ajuste_salida',
        ];
    
        if ($this->tipoMovimiento === 'transferencia') {
            $rules = array_merge($rules, [
                'ubicacionOrigenId' => 'required|integer|exists:ubicaciones,id',
                'zonaOrigenId' => 'required|integer|exists:zonas,id',
                'ubicacionDestinoId' => 'required|integer|exists:ubicaciones,id|different:ubicacionOrigenId',
                'zonaDestinoId' => 'required|integer|exists:zonas,id',
            ]);
        } else { // Para ajuste_entrada y ajuste_salida
            $rules = array_merge($rules, [
                'ubicacionAfectadaId' => 'required|integer|exists:ubicaciones,id',
                'zonaAfectadaId' => 'required|integer|exists:zonas,id',
                'motivoAjuste' => 'required|string|min:5',
            ]);
            
            if ($this->tipoMovimiento === 'ajuste_entrada') {
                $rules['precioCompraUnitario'] = 'required|numeric|min:0';
                $rules['selectedProveedorId'] = 'nullable|integer|exists:proveedores,id';
            }
        }
    
        $this->validate($rules);
    
        $producto = Producto::find($this->selectedProductId);
    
        if ($this->tipoMovimiento === 'transferencia') {
            $pivot = $producto->ubicaciones()->where('ubicacion_id', $this->ubicacionOrigenId)
                               ->where('zona_id', $this->zonaOrigenId)
                               ->first();
    
            if (!$pivot || $pivot->pivot->stock < $this->cantidad) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Stock insuficiente en la ubicación/zona de origen.']);
                return;
            }
    
            // Disminuir stock en origen
            $producto->ubicaciones()->wherePivot('zona_id', $this->zonaOrigenId)->updateExistingPivot($this->ubicacionOrigenId, [
                'stock' => $pivot->pivot->stock - $this->cantidad,
            ]);
    
            // Aumentar o crear stock en destino
            $pivotDestino = $producto->ubicaciones()->where('ubicacion_id', $this->ubicacionDestinoId)
                                    ->where('zona_id', $this->zonaDestinoId)
                                    ->first();
    
            if ($pivotDestino) {
                $producto->ubicaciones()->wherePivot('zona_id', $this->zonaDestinoId)->updateExistingPivot($this->ubicacionDestinoId, [
                    'stock' => $pivotDestino->pivot->stock + $this->cantidad,
                ]);
            } else {
                $producto->ubicaciones()->attach($this->ubicacionDestinoId, [
                    'stock' => $this->cantidad,
                    'zona_id' => $this->zonaDestinoId,
                    'stock_minimo' => 0,
                ]);
            }
    
            // Crear registro de movimiento
            MovimientoStock::create([
                'producto_id' => $this->selectedProductId,
                'tipo' => $this->tipoMovimiento,
                'cantidad' => $this->cantidad,
                'ubicacion_origen_id' => $this->ubicacionOrigenId,
                'zona_origen_id' => $this->zonaOrigenId,
                'ubicacion_destino_id' => $this->ubicacionDestinoId,
                'zona_destino_id' => $this->zonaDestinoId,
                'user_id' => auth()->id(),
            ]);
    
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Transferencia de stock realizada exitosamente.']);
    
        } else {
            // Lógica para ajustes de entrada/salida
            $this->processStockMovement($producto);
        }
    
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->reset([
            'searchTerm',
            'selectedProductId',
            'selectedProductName',
            'cantidad',
            'precioCompraUnitario',
            'selectedProveedorId',
            'referenciaVenta',
            'motivoAjuste',
            'ubicacionOrigenId',
            'zonaOrigenId',
            'ubicacionDestinoId',
            'zonaDestinoId',
            'ubicacionAfectadaId',
            'zonaAfectadaId',
            'stockExistenteOrigen',
            'stockExistenteDestino',
            'zonasOrigen',
            'zonasDestino',
            'zonasAfectadas',
        ]);
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
        $cantidadCambio = 0;
        if ($this->tipoMovimiento === 'ajuste_entrada') {
            $cantidadCambio = $this->cantidad;
        } elseif ($this->tipoMovimiento === 'ajuste_salida') {
            $cantidadCambio = -$this->cantidad;
        }
    
        $pivot = $producto->ubicaciones()
                          ->where('ubicacion_id', $this->ubicacionAfectadaId)
                          ->where('zona_id', $this->zonaAfectadaId)
                          ->first();
    
        // Para ajuste de salida, el pivot debe existir y tener suficiente stock
        if ($this->tipoMovimiento === 'ajuste_salida' && (!$pivot || $pivot->pivot->stock < $this->cantidad)) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Stock insuficiente en la ubicación/zona seleccionada.']);
            return;
        }
    
        if ($pivot) {
            // Si el pivot existe, se actualiza
            $producto->ubicaciones()->wherePivot('zona_id', $this->zonaAfectadaId)->updateExistingPivot($this->ubicacionAfectadaId, [
                'stock' => $pivot->pivot->stock + $cantidadCambio,
            ]);
        } else {
            // Si no existe (solo posible para ajuste_entrada), se crea
            if ($this->tipoMovimiento === 'ajuste_entrada') {
                $producto->ubicaciones()->attach($this->ubicacionAfectadaId, [
                    'stock' => $cantidadCambio,
                    'zona_id' => $this->zonaAfectadaId,
                    'stock_minimo' => 0,
                ]);
            }
        }
    
        // Crear registro de movimiento
        MovimientoStock::create([
            'producto_id' => $this->selectedProductId,
            'tipo' => $this->tipoMovimiento,
            'cantidad' => $this->cantidad, // Siempre guardar cantidad positiva
            'motivo_ajuste' => $this->motivoAjuste,
            'ubicacion_origen_id' => $this->ubicacionAfectadaId,
            'zona_origen_id' => $this->zonaAfectadaId,
            'ubicacion_destino_id' => $this->ubicacionAfectadaId,
            'zona_destino_id' => $this->zonaAfectadaId,
            'user_id' => auth()->id(),
            'precio_compra_unitario' => $this->precioCompraUnitario,
            'proveedor_id' => $this->selectedProveedorId,
        ]);
    
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Ajuste de stock realizado exitosamente.']);
    }

    public function render()
    {
        $movimientos = MovimientoStock::with(['producto', 'proveedor', 'ubicacionOrigen', 'zonaOrigen', 'ubicacionDestino', 'zonaDestino', 'user'])
            ->latest()
            ->paginate(10);

        return view('livewire.gestionar-movimientos-stock', [
            'movimientos' => $movimientos,
        ]);
    }
}