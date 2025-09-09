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
use App\Models\Ubicacion; // Importar Ubicacion
use App\Models\Zona; // Importar Zona
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
    public $proveedor_id;
    public $fecha_factura;
    public $tasa_de_cambio_id;
    public $tasa_cambio_aplicada_valor = 0;

    // Product search and list
    public $searchProducto = '';
    public $productosEncontrados = [];
    public $productosFactura = [];
    public $quantities = [];

    // Ubicacion and Zona for adding products
    public $almacenes = [];
    public $zonasDisponibles = [];
    public $ubicacion_id_para_agregar;
    public $zona_id_para_agregar;
    public $zonas = [];


    // Payment methods
    public $metodosPagoDisponibles = [];
    public $pagosFactura = [];

    // Totals
    public $totalFacturaUsd = 0;
    public $totalFacturaBs = 0;

    // For product creation modal
    public $showCrearProductoModal = false;
    public $showCrearZonaModal = false;
    public $currentUbicacionIdForZonaModal = null;

    public function mount(FacturaCompra $factura = null)
    {
        try {
            $this->loadMetodosPagoDisponibles();
            $this->almacenes = Ubicacion::whereIn('tipo', ['almacen', 'transito'])->orderBy('nombre')->get();
            $this->zonas = Zona::orderBy('nombre')->get(); // Cargar todas las zonas
    
            

            if ($factura && $factura->exists) {
                $this->factura_id = $factura->id;
                $this->proveedor_id = $factura->proveedor_id;
                $this->fecha_factura = $factura->fecha_factura->format('Y-m-d');
                $this->tasa_de_cambio_id = $factura->tasa_de_cambio_id;
                $this->tasa_cambio_aplicada_valor = $factura->tasaDeCambio ? $factura->tasaDeCambio->tasa : 0;

                foreach ($factura->detalles as $detalle) {
                    $productoOriginal = Producto::find($detalle->producto_id);
                    $this->productosFactura[] = [
                        'producto_id' => $detalle->producto_id,
                        'nombre' => $detalle->producto->nombre,
                        'cantidad' => $detalle->cantidad,
                        'precio_compra_unitario' => $detalle->precio_compra_unitario,
                        'precio_compra_original' => $productoOriginal ? $productoOriginal->precio_compra : 0,
                        'actualizar_precio' => false,
                        'subtotal_usd' => $detalle->subtotal_usd,
                        'ubicacion_id' => $detalle->ubicacion_id, // Cargar ubicación
                        'zona_id' => $detalle->zona_id,           // Cargar zona
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

                // Set default location to 'En Transito' if available
                $enTransitoUbicacion = Ubicacion::where('tipo', 'transito')->first();
                if ($enTransitoUbicacion) {
                    $this->ubicacion_id_para_agregar = $enTransitoUbicacion->id;
                    $firstZona = $enTransitoUbicacion->zonas()->first();
                    if ($firstZona) {
                        $this->zona_id_para_agregar = $firstZona->id;
                    }
                    $this->updatedUbicacionIdParaAgregar($this->ubicacion_id_para_agregar); // ADDED THIS LINE
                } else if ($this->almacenes->isNotEmpty()) {
                    // Fallback to first warehouse if 'transito' not found
                    $this->ubicacion_id_para_agregar = $this->almacenes->first()->id;
                    $this->updatedUbicacionIdParaAgregar($this->ubicacion_id_para_agregar);
                    \Log::info('CrearEditarFacturaCompra: Default set to first Almacen. Ubicacion ID: ' . $this->ubicacion_id_para_agregar . ', Zona ID: ' . $this->zona_id_para_agregar);
                } else {
                    \Log::warning('CrearEditarFacturaCompra: No default location (En Transito or Almacen) could be set.');
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('app-notification-error', message: 'Error al cargar la factura: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Error in CrearEditarFacturaCompra mount: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    public function openCrearZonaModal($ubicacionId = null)
    {
        $this->currentUbicacionIdForZonaModal = $ubicacionId;
        $this->showCrearZonaModal = true;
    }

    #[On('close-crear-zona-modal')]
    public function closeCrearZonaModal()
    {
        $this->showCrearZonaModal = false;
        $this->currentUbicacionIdForZonaModal = null;
    }

    public function render()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        return view('livewire.crear-editar-factura-compra', [
            'proveedores' => $proveedores,
        ]);
    }

    public function updatedUbicacionIdParaAgregar($ubicacion_id)
    {
        $this->zonasDisponibles = Zona::where('ubicacion_id', $ubicacion_id)->orderBy('nombre')->get();
        $this->zona_id_para_agregar = null; // Reset zona selection
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
                'precio_compra_original' => $producto->precio_compra,
                'actualizar_precio' => false,
                'subtotal_usd' => $producto->precio_compra * $quantity,
                'ubicacion_id' => $this->ubicacion_id_para_agregar, // New
                'zona_id' => $this->zona_id_para_agregar, // New
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
        $field = $parts[1] ?? null;

        if (isset($this->productosFactura[$index])) {
            // Si cambia la ubicación, reseteamos la zona
            if ($field === 'ubicacion_id') {
                $this->productosFactura[$index]['zona_id'] = null;
            }

            $cantidad = (float) ($this->productosFactura[$index]['cantidad'] ?? 0);
            $precio = (float) ($this->productosFactura[$index]['precio_compra_unitario'] ?? 0);

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
            'tasa_de_cambio_id' => 'required|exists:tasas_de_cambio,id',
            'productosFactura' => 'required|array|min:1',
            'productosFactura.*.cantidad' => 'required|numeric|min:1',
            'productosFactura.*.precio_compra_unitario' => 'required|numeric|min:0',
            'productosFactura.*.ubicacion_id' => 'required|exists:ubicaciones,id',
            'pagosFactura.*.metodo_pago_id' => 'required|exists:metodo_pagos,id',
            'pagosFactura.*.monto_usd' => 'required|numeric|min:0',
        ]);

        if ($this->tasa_cambio_aplicada_valor <= 0) {
            $this->dispatch('app-notification-error', message: 'Tasa de cambio no válida para el cálculo.');
            return;
        }

        DB::transaction(function () {
            if ($this->factura_id) {
                $factura = FacturaCompra::find($this->factura_id);

                // Revert stock from previous details
                foreach ($factura->detalles as $detalle) {
                    if ($detalle->producto && $detalle->ubicacion_id) {
                        DB::table('producto_ubicacion')->where([
                            ['producto_id', '=', $detalle->producto_id],
                            ['ubicacion_id', '=', $detalle->ubicacion_id],
                            ['zona_id', '=', $detalle->zona_id],
                        ])->decrement('stock', $detalle->cantidad);
                    }
                }

                $factura->detalles()->delete();
                $factura->pagos()->delete();

                DB::table('factura_compras')->where('id', $factura->id)->update([
                    'proveedor_id' => $this->proveedor_id,
                    'fecha_factura' => $this->fecha_factura,
                    'tasa_de_cambio_id' => $this->tasa_de_cambio_id,
                    'total_usd' => $this->totalFacturaUsd,
                    'total_bs' => $this->totalFacturaBs,
                    'user_id' => Auth::id(),
                    'updated_at' => now(),
                ]);

            } else {
                $factura = FacturaCompra::create([
                    'proveedor_id' => $this->proveedor_id,
                    'fecha_factura' => $this->fecha_factura,
                    'tasa_de_cambio_id' => $this->tasa_de_cambio_id,
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
                    'ubicacion_id' => $item['ubicacion_id'],
                    'zona_id' => $item['zona_id'] ?? null,
                ]);

                // Correct stock update logic
                $stock = DB::table('producto_ubicacion')->where([
                    ['producto_id', '=', $item['producto_id']],
                    ['ubicacion_id', '=', $item['ubicacion_id']],
                    ['zona_id', '=', $item['zona_id'] ?? null],
                ])->first();

                if ($stock) {
                    DB::table('producto_ubicacion')->where('id', $stock->id)->increment('stock', $item['cantidad']);
                } else {
                    DB::table('producto_ubicacion')->insert([
                        'producto_id' => $item['producto_id'],
                        'ubicacion_id' => $item['ubicacion_id'],
                        'zona_id' => $item['zona_id'] ?? null,
                        'stock' => $item['cantidad'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if (isset($item['actualizar_precio']) && $item['actualizar_precio']) {
                    Producto::find($item['producto_id'])->update([
                        'precio_compra' => $item['precio_compra_unitario']
                    ]);
                }
            }

            foreach ($this->pagosFactura as $pago) {
                FacturaCompraMetodoPago::create([
                    'factura_compra_id' => $factura->id,
                    'metodo_pago_id' => $pago['metodo_pago_id'],
                    'monto_usd' => $pago['monto_usd'],
                ]);
            }

            session()->flash('success', 'Factura ' . ($this->factura_id ? 'actualizada' : 'creada') . ' exitosamente.');
            $this->redirect(route('facturas-compra.index'));
        });
    }

    public function openCrearProductoModal()
    {
        $this->showCrearProductoModal = true;
    }

    #[On('productoCreated')]
    public function handleProductoCreated($productoId)
    {
        $this->showCrearProductoModal = false;
        $this->addProducto($productoId);
        $this->dispatch('app-notification-success', message: 'Producto creado y añadido.');
    }

    #[On('zonaCreated')]
    public function handleZonaCreated($zonaId, $zonaNombre, $ubicacionId)
    {
        // Refresh the zones collection
        $this->zonas = Zona::orderBy('nombre')->get();
        $this->dispatch('app-notification-success', message: 'Zona "' . $zonaNombre . '" creada y disponible.');

        // Optional: Try to select the newly created zone for the relevant product item
        // This part is complex without knowing which product item triggered the modal.
        // For now, we just refresh the list and let the user select it.
        // If automatic selection is critical, we'd need to pass the product index to the modal.
    }
}