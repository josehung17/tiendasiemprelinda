<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\MetodoPago;
use App\Models\TasaDeCambio;
use App\Models\FacturaCompra;
use App\Models\FacturaCompraDetalle;
use App\Models\FacturaCompraMetodoPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;

class CrearEditarFacturaCompra extends Component
{
    // Form properties
    #[Rule('required|exists:proveedores,id')]
    public $proveedor_id;

    #[Rule('required|date')]
    public $fecha_factura;

    public $tasa_cambio_aplicada; // Will be fetched based on fecha_factura

    // Product search and list
    public $searchProducto = '';
    public $productosEncontrados = [];
    public $productosFactura = []; // Array of ['producto_id', 'nombre', 'cantidad', 'precio_compra_unitario', 'subtotal_usd']
    public $quantities = []; // To store quantities for products found in search

    // Payment methods
    public $metodosPagoDisponibles = [];
    public $pagosFactura = []; // Array of ['metodo_pago_id', 'monto_usd']

    // Totals
    public $totalFacturaUsd = 0;
    public $totalFacturaBs = 0;

    // For product creation modal
    public $showCrearProductoModal = false;

    public function mount()
    {
        $this->fecha_factura = Carbon::now()->format('Y-m-d');
        $this->loadMetodosPagoDisponibles();
        $this->fetchTasaDeCambio(); // Fetch initial rate
    }

    public function render()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        return view('livewire.crear-editar-factura-compra', [
            'proveedores' => $proveedores,
        ]);
    }

    public function updatedSearchProducto()
    {
        if (!empty($this->searchProducto)) {
            $this->productosEncontrados = Producto::where('nombre', 'like', '%' . $this->searchProducto . '%')
                ->limit(10)
                ->get();
        } else {
            $this->productosEncontrados = [];
        }
    }

    public function addProducto($productoId)
    {
        $producto = Producto::find($productoId);
        if ($producto && !collect($this->productosFactura)->contains('producto_id', $productoId)) {
            $quantity = $this->quantities[$productoId] ?? 1; // Get quantity from the quantities array, default to 1

            // Basic validation for quantity
            if (!is_numeric($quantity) || $quantity <= 0) {
                $this->dispatch('app-notification-error', message: 'La cantidad debe ser un número positivo.');
                return;
            }

            $this->productosFactura[] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'cantidad' => $quantity,
                'precio_compra_unitario' => $producto->precio_compra, // Default to product's last purchase price
                'subtotal_usd' => $producto->precio_compra * $quantity,
            ];
            $this->searchProducto = '';
            $this->productosEncontrados = [];
            // No need to reset quantities[$productoId] here, as it's handled by wire:model.live
            $this->calcularTotales();
        }
    }

    public function removeProducto($index)
    {
        unset($this->productosFactura[$index]);
        $this->productosFactura = array_values($this->productosFactura); // Re-index array
        $this->calcularTotales();
    }

    public function updatedProductosFactura($value, $key)
    {
        // $key format: "index.property" e.g., "0.cantidad"
        $parts = explode('.', $key);
        $index = $parts[0];
        $property = $parts[1];

        if (isset($this->productosFactura[$index])) {
            $cantidad = (float) $this->productosFactura[$index]['cantidad'];
            $precio = (float) $this->productosFactura[$index]['precio_compra_unitario'];

            if ($cantidad < 0) $cantidad = 0;
            if ($precio < 0) $precio = 0;

            $this->productosFactura[$index]['cantidad'] = $cantidad;
            $this->productosFactura[$index]['precio_compra_unitario'] = $precio;
            $this->productosFactura[$index]['subtotal_usd'] = round($cantidad * $precio, 2);
            $this->calcularTotales();
        }
    }

    public function addPago()
    {
        $this->pagosFactura[] = [
            'metodo_pago_id' => '',
            'monto_usd' => 0,
        ];
    }

    public function removePago($index)
    {
        unset($this->pagosFactura[$index]);
        $this->pagosFactura = array_values($this->pagosFactura);
    }

    public function loadMetodosPagoDisponibles()
    {
        $this->metodosPagoDisponibles = MetodoPago::orderBy('nombre')->get();
    }

    public function updatedFechaFactura()
    {
        $this->fetchTasaDeCambio();
    }

    public function fetchTasaDeCambio()
    {
        $fecha = Carbon::parse($this->fecha_factura)->endOfDay(); // Get rate for end of selected day

        $tasa = TasaDeCambio::whereDate('fecha_actualizacion', '<=', $fecha)
                            ->orderBy('fecha_actualizacion', 'desc')
                            ->first();

        if ($tasa) {
            $this->tasa_cambio_aplicada = $tasa->tasa;
            $this->dispatch('app-notification-success', message: 'Tasa de cambio para ' . $fecha->format('d/m/Y') . ' cargada: ' . number_format($tasa->tasa, 4));
        } else {
            $this->tasa_cambio_aplicada = 0; // Or a default, or force user to select a date with a rate
            $this->dispatch('app-notification-error', message: 'No se encontró tasa de cambio para la fecha seleccionada o anterior. Por favor, actualice la tasa manualmente si es necesario.');
        }
        $this->calcularTotales();
    }

    public function calcularTotales()
    {
        $this->totalFacturaUsd = collect($this->productosFactura)->sum('subtotal_usd');
        $this->totalFacturaBs = round($this->totalFacturaUsd * $this->tasa_cambio_aplicada, 2);
    }

    public function saveFactura()
    {
        $this->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_factura' => 'required|date',
            'productosFactura' => 'required|array|min:1',
            'productosFactura.*.producto_id' => 'required|exists:productos,id',
            'productosFactura.*.cantidad' => 'required|numeric|min:1',
            'productosFactura.*.precio_compra_unitario' => 'required|numeric|min:0',
            'pagosFactura' => 'nullable|array',
            'pagosFactura.*.metodo_pago_id' => 'required|exists:metodo_pagos,id',
            'pagosFactura.*.monto_usd' => 'required|numeric|min:0',
        ]);

        if ($this->tasa_cambio_aplicada <= 0) {
            $this->dispatch('app-notification-error', message: 'No se ha podido determinar una tasa de cambio válida para la factura.');
            return;
        }

        DB::transaction(function () {
            $factura = FacturaCompra::create([
                'proveedor_id' => $this->proveedor_id,
                'fecha_factura' => $this->fecha_factura,
                'tasa_cambio_aplicada' => $this->tasa_cambio_aplicada,
                'total_usd' => $this->totalFacturaUsd,
                'total_bs' => $this->totalFacturaBs,
                'user_id' => Auth::id(),
            ]);

            foreach ($this->productosFactura as $item) {
                FacturaCompraDetalle::create([
                    'factura_compra_id' => $factura->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_compra_unitario' => $item['precio_compra_unitario'],
                    'subtotal_usd' => $item['subtotal_usd'],
                ]);

                // Update product stock and last purchase price
                $producto = Producto::find($item['producto_id']);
                if ($producto) {
                    $producto->stock += $item['cantidad'];
                    $producto->precio_compra = $item['precio_compra_unitario']; // Update last purchase price
                    $producto->save();

                    // Optionally, record stock movement
                    // MovimientoStock::create([...]);
                }
            }

            foreach ($this->pagosFactura as $pago) {
                FacturaCompraMetodoPago::create([
                    'factura_compra_id' => $factura->id,
                    'metodo_pago_id' => $pago['metodo_pago_id'],
                    'monto_usd' => $pago['monto_usd'],
                ]);
            }
        });

        $this->dispatch('app-notification-success', message: 'Factura de compra registrada y stock actualizado exitosamente.');
        $this->resetForm();
        $this->dispatch('facturaCompraSaved'); // Notify list component
    }

    public function resetForm()
    {
        $this->proveedor_id = null;
        $this->fecha_factura = Carbon::now()->format('Y-m-d');
        $this->tasa_cambio_aplicada = 0;
        $this->searchProducto = '';
        $this->productosEncontrados = [];
        $this->productosFactura = [];
        $this->pagosFactura = [];
        $this->totalFacturaUsd = 0;
        $this->totalFacturaBs = 0;
        $this->fetchTasaDeCambio(); // Re-fetch initial rate
    }

    public function openCrearProductoModal()
    {
        $this->showCrearProductoModal = true;
        $this->dispatch('openCrearProductoModal'); // Event to open the modal
    }

    #[On('productoCreated')]
    public function handleProductoCreated($productoId)
    {
        $producto = Producto::find($productoId);
        if ($producto) {
            $this->addProducto($productoId); // Add newly created product to the list
            $this->dispatch('app-notification-success', message: 'Producto "' . $producto->nombre . '" creado y añadido a la factura.');
        }
        $this->showCrearProductoModal = false;
    }
}