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
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

class CrearEditarFacturaCompra extends Component
{
    // Invoice state
    public $factura_id;

    // Form properties
    // #[Rule('required|exists:proveedores,id')]
    public $proveedor_id;

    // #[Rule('required|date')]
    public $fecha_factura;

    public $tasa_de_cambio_id; // Nueva propiedad para almacenar el ID de la tasa de cambio
    public $tasa_cambio_aplicada_valor = 0; // Para mostrar el valor, derivado de tasa_de_cambio_id

    // Product search and list
    public $searchProducto = '';
    public $productosEncontrados = [];
    public $productosFactura = [];
    public $quantities = [];

    // Payment methods
    public $metodosPagoDisponibles = [];
    public $pagosFactura = [];

    // Totals
    public $totalFacturaUsd = 0;
    public $totalFacturaBs = 0;

    // For product creation modal
    public $showCrearProductoModal = false;

    public function mount(FacturaCompra $factura = null)
    {
        try {
            $this->loadMetodosPagoDisponibles();

            if ($factura && $factura->exists) {
                $this->factura_id = $factura->id;
                $this->proveedor_id = $factura->proveedor_id;
                $this->fecha_factura = $factura->fecha_factura->format('Y-m-d');
                $this->tasa_de_cambio_id = $factura->tasa_de_cambio_id; // Load the ID
                $this->tasa_cambio_aplicada_valor = $factura->tasaDeCambio ? $factura->tasaDeCambio->tasa : 0; // Get value from relation

                foreach ($factura->detalles as $detalle) {
                    $this->productosFactura[] = [
                        'producto_id' => $detalle->producto_id,
                        'nombre' => $detalle->producto->nombre,
                        'cantidad' => $detalle->cantidad,
                        'precio_compra_unitario' => $detalle->precio_compra_unitario,
                        'subtotal_usd' => $detalle->subtotal_usd,
                    ];
                }

                foreach ($factura->pagos as $pago) {
                    $this->pagosFactura[] = [
                        'metodo_pago_id' => $pago->metodo_pago_id,
                        'monto_usd' => $pago->monto_usd,
                    ];
                }

                $this->calcularTotales();
            } else {
                $this->fecha_factura = Carbon::now()->format('Y-m-d');
                $this->fetchTasaDeCambio();
            }
        } catch (\Exception $e) {
            $this->dispatch('app-notification-error', message: 'Error al cargar la factura: ' . $e->getMessage());
            // Log the error for server-side debugging
            \Illuminate\Support\Facades\Log::error('Error in CrearEditarFacturaCompra mount: ' . $e->getMessage(), ['exception' => $e]);
        }
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
            $quantity = $this->quantities[$productoId] ?? 1;

            if (!is_numeric($quantity) || $quantity <= 0) {
                $this->dispatch('app-notification-error', message: 'La cantidad debe ser un número positivo.');
                return;
            }

            $this->productosFactura[] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'cantidad' => $quantity,
                'precio_compra_unitario' => $producto->precio_compra,
                'subtotal_usd' => $producto->precio_compra * $quantity,
            ];
            $this->searchProducto = '';
            $this->productosEncontrados = [];
            $this->calcularTotales();
        }
    }

    public function removeProducto($index)
    {
        unset($this->productosFactura[$index]);
        $this->productosFactura = array_values($this->productosFactura);
        $this->calcularTotales();
    }

    public function updatedProductosFactura($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];

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
        $this->pagosFactura[] = ['metodo_pago_id' => '', 'monto_usd' => 0];
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
        try {
            $fecha = Carbon::parse($this->fecha_factura)->endOfDay();
            $tasaDeCambio = TasaDeCambio::whereDate('fecha_actualizacion', '<=', $fecha)
                                ->orderBy('fecha_actualizacion', 'desc')
                                ->first();

            if ($tasaDeCambio) {
                $this->tasa_de_cambio_id = $tasaDeCambio->id;
                $this->tasa_cambio_aplicada_valor = $tasaDeCambio->tasa;
            } else {
                $this->tasa_de_cambio_id = null;
                $this->tasa_cambio_aplicada_valor = 0;
                $this->dispatch('app-notification-error', message: 'No se encontró tasa de cambio para la fecha seleccionada.');
            }
            $this->calcularTotales();
        } catch (\Exception $e) {
            $this->dispatch('app-notification-error', message: 'Error al obtener la tasa de cambio: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Error in fetchTasaDeCambio: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    public function calcularTotales()
    {
        $tasaAplicada = $this->tasa_cambio_aplicada_valor;
        if ($tasaAplicada <= 0 && $this->tasa_de_cambio_id) {
            // Fallback: if value is 0 but ID exists, try to get value from DB
            $tasaObj = TasaDeCambio::find($this->tasa_de_cambio_id);
            if ($tasaObj) {
                $tasaAplicada = $tasaObj->tasa;
                $this->tasa_cambio_aplicada_valor = $tasaAplicada; // Update property
            }
        }

        $this->totalFacturaUsd = collect($this->productosFactura)->sum('subtotal_usd');
        $this->totalFacturaBs = round($this->totalFacturaUsd * $tasaAplicada, 2);
    }

    public function save()
    {
        $this->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_factura' => 'required|date',
            'tasa_de_cambio_id' => 'required|exists:tasas_de_cambio,id', // Validate the ID
            'productosFactura' => 'required|array|min:1',
            'productosFactura.*.cantidad' => 'required|numeric|min:1',
            'productosFactura.*.precio_compra_unitario' => 'required|numeric|min:0',
            'pagosFactura.*.metodo_pago_id' => 'required|exists:metodo_pagos,id',
            'pagosFactura.*.monto_usd' => 'required|numeric|min:0',
        ]);

        if ($this->tasa_cambio_aplicada_valor <= 0) {
            $this->dispatch('app-notification-error', message: 'Tasa de cambio no válida para el cálculo.');
            return;
        }

        DB::transaction(function () {
            if ($this->factura_id) {
                // Update logic
                $factura = FacturaCompra::find($this->factura_id);

                // Revert stock from previous details
                foreach ($factura->detalles as $detalle) {
                    if ($detalle->producto) {
                        $detalle->producto->stock -= $detalle->cantidad;
                        $detalle->producto->save();
                    }
                }

                // Delete old details and payments
                $factura->detalles()->delete();
                $factura->pagos()->delete();

                // Use Query Builder update to bypass potential Eloquent model issues
                DB::table('factura_compras')->where('id', $factura->id)->update([
                    'proveedor_id' => $this->proveedor_id,
                    'fecha_factura' => $this->fecha_factura,
                    'tasa_de_cambio_id' => $this->tasa_de_cambio_id, // Save the ID
                    'total_usd' => $this->totalFacturaUsd,
                    'total_bs' => $this->totalFacturaBs,
                    'user_id' => Auth::id(),
                    'updated_at' => now(),
                ]);

            } else {
                // Create logic
                $factura = FacturaCompra::create([
                    'proveedor_id' => $this->proveedor_id,
                    'fecha_factura' => $this->fecha_factura,
                    'tasa_de_cambio_id' => $this->tasa_de_cambio_id, // Save the ID
                    'total_usd' => $this->totalFacturaUsd,
                    'total_bs' => $this->totalFacturaBs,
                    'user_id' => Auth::id(),
                ]);
            }

            // Create new details and update stock
            foreach ($this->productosFactura as $item) {
                FacturaCompraDetalle::create([
                    'factura_compra_id' => $factura->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_compra_unitario' => $item['precio_compra_unitario'],
                    'subtotal_usd' => $item['subtotal_usd'],
                ]);

                $producto = Producto::find($item['producto_id']);
                if ($producto) {
                    $producto->stock += $item['cantidad'];
                    $producto->precio_compra = $item['precio_compra_unitario'];
                    $producto->save();
                }
            }

            // Create new payments
            foreach ($this->pagosFactura as $pago) {
                FacturaCompraMetodoPago::create([
                    'factura_compra_id' => $factura->id,
                    'metodo_pago_id' => $pago['metodo_pago_id'],
                    'monto_usd' => $pago['monto_usd'],
                ]);
            }

            session()->flash('message', 'Factura ' . ($this->factura_id ? 'actualizada' : 'creada') . ' exitosamente.');
            $this->redirect(route('facturas-compra.index'));
        });
    }

    public function openCrearProductoModal()
    {
        $this->showCrearProductoModal = true;
    }

    // #[On('productoCreated')]
    public function handleProductoCreated($productoId)
    {
        $this->showCrearProductoModal = false;
        $this->addProducto($productoId);
        $this->dispatch('app-notification-success', message: 'Producto creado y añadido.');
    }
}