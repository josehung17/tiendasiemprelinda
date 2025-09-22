<div>
    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Proveedor y Fecha --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="proveedor_id" :value="__('Proveedor')" />
                <x-select-input id="proveedor_id" wire:model="proveedor_id" class="mt-1 block w-full">
                    <option value="">{{ __('Selecciona un proveedor') }}</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error class="mt-2" :messages="$errors->get('proveedor_id')" />
            </div>
            <div>
                <x-input-label for="fecha_factura" :value="__('Fecha de Factura')" />
                <x-text-input id="fecha_factura" wire:model.blur="fecha_factura" type="date" class="mt-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('fecha_factura')" />
                @if($tasa_cambio_aplicada_valor > 0)
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Tasa de cambio aplicada: <strong>Bs. {{ number_format($tasa_cambio_aplicada_valor, 4) }}</strong></p>
                @else
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">No se encontró tasa de cambio para esta fecha.</p>
                @endif
            </div>
        </div>

        {{-- Productos de la Factura --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Productos</h4>
            <div class="flex items-center space-x-4 mb-4">
                <x-text-input wire:model.live.debounce.300ms="searchProducto" type="text" placeholder="Buscar producto por nombre o código..." class="flex-grow" />
                <x-primary-button type="button" wire:click="openCrearProductoModal">Crear Producto</x-primary-button>
            </div>

            {{-- Ubicacion y Zona para agregar (eliminado) --}}

            @if(!empty($productosEncontrados))
                <ul class="border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 shadow-lg mb-4 max-h-60 overflow-y-auto">
                    @foreach($productosEncontrados as $producto)
                        <li class="p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 flex items-center space-x-3">
                            @if($producto->ruta_imagen)
                                <img src="{{ asset('storage/' . $producto->ruta_imagen) }}" alt="{{ $producto->nombre }}" class="w-10 h-10 object-cover rounded">
                            @else
                                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded">
                                    <span class="text-gray-500 dark:text-gray-400 text-xs">No Img</span>
                                </div>
                            @endif
                            <div class="flex-grow">
                                <p class="font-semibold text-sm text-gray-900 dark:text-gray-100">{{ $producto->nombre }}</p>
                                <p class="text-gray-700 dark:text-gray-300 text-xs">Precio Compra: ${{ number_format($producto->precio_compra, 2) }}</p>
                            </div>
                            <input type="number" wire:model.live="quantities.{{ $producto->id }}" min="1" value="1" class="w-16 text-center border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <x-primary-button type="button" wire:click="addProducto({{ $producto->id }})">Agregar</x-primary-button>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-4">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ubicación</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zona</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio Unitario ($)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subtotal ($)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Act. P.</th>
                            <th class="px-2 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($productosFactura as $index => $item)
                            <tr wire:key="producto-{{ $index }}">
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $item['nombre'] }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <x-select-input wire:model.live="productosFactura.{{ $index }}.ubicacion_id" class="w-full text-sm">
                                        <option value="">Seleccionar</option>
                                        @foreach($almacenes as $almacen)
                                            <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                                        @endforeach
                                    </x-select-input>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <x-select-input wire:model="productosFactura.{{ $index }}.zona_id" class="w-full text-sm" :disabled="empty($item['ubicacion_id'])">
                                            <option value="">Seleccionar</option>
                                            @foreach($zonas->where('ubicacion_id', $item['ubicacion_id']) as $zona)
                                                <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                                            @endforeach
                                        </x-select-input>
                                        <button type="button" wire:click="openCrearZonaModal(productosFactura[{{ $index }}].ubicacion_id)" class="p-2 rounded-md bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-xs" title="Crear nueva zona" :disabled="empty($item['ubicacion_id'])">
                                            +
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <x-text-input wire:model.blur="productosFactura.{{ $index }}.cantidad" type="number" min="1" class="w-20" />
                                    <x-input-error :messages="$errors->get('productosFactura.' . $index . '.cantidad')" />
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <x-text-input wire:model.blur="productosFactura.{{ $index }}.precio_compra_unitario" type="number" step="0.01" min="0" class="w-24" />
                                    <x-input-error :messages="$errors->get('productosFactura.' . $index . '.precio_compra_unitario')" />
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">${{ number_format($item['subtotal_usd'], 2) }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    @if(isset($item['precio_compra_original']) && $item['precio_compra_unitario'] != $item['precio_compra_original'])
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Orig: ${{ number_format($item['precio_compra_original'], 2) }}</span>
                                            <input type="checkbox" wire:model.live="productosFactura.{{ $index }}.actualizar_precio" class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out" />
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500 dark:text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button type="button" wire:click="removeProducto({{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 text-xl">&times;</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                    No hay productos en la factura.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('productosFactura')" />
        </div>

        {{-- Métodos de Pago --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Métodos de Pago</h4>
            <div class="space-y-4 mb-4">
                @foreach($pagosFactura as $index => $pago)
                    <div class="flex items-center space-x-4">
                        <x-select-input wire:model="pagosFactura.{{ $index }}.metodo_pago_id" class="flex-grow">
                            <option value="">{{ __('Selecciona método de pago') }}</option>
                            @foreach ($metodosPagoDisponibles->groupBy('cuenta.moneda.nombre') as $monedaNombre => $metodosPorMoneda)
                                <optgroup label="{{ $monedaNombre }}">
                                    @foreach ($metodosPorMoneda as $metodo)
                                        <option value="{{ $metodo->id }}">{{ $metodo->cuenta->nombre }} - {{ $metodo->nombre }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </x-select-input>
                        <x-text-input wire:model.live="pagosFactura.{{ $index }}.monto_usd" type="number" step="0.01" min="0" placeholder="{{ $this->getMontoPlaceholder($pago['metodo_pago_id']) }}" class="w-32" />
                        <button type="button" wire:click="removePago({{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 text-xl">&times;</button>
                    </div>
                    <x-input-error :messages="$errors->get('pagosFactura.' . $index . '.metodo_pago_id')" />
                    <x-input-error :messages="$errors->get('pagosFactura.' . $index . '.monto_usd')" />
                @endforeach
            </div>
            <x-secondary-button type="button" wire:click="addPago">Agregar Método de Pago</x-secondary-button>
            <x-input-error :messages="$errors->get('pagosFactura')" />
        </div>

        {{-- Totales --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 text-right">
            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total USD: ${{ number_format($totalFacturaUsd, 2) }}</p>
            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Bs: Bs. {{ number_format($totalFacturaBs, 2) }}</p>
        </div>

        {{-- Botón de Guardar --}}
        <div class="flex items-center justify-end mt-4">
            <x-primary-button>{{ $factura_id ? __('Actualizar Factura') : __('Registrar Factura y Actualizar Stock') }}</x-primary-button>
        </div>
    </form>

    {{-- Modal para Crear Producto --}}
    @if($showCrearProductoModal)
        <x-modal name="crear-producto-modal" :show="$showCrearProductoModal" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Crear Nuevo Producto</h2>
                @livewire('gestionar-creacion-producto')
                <div class="mt-6 flex justify-end">
                    <x-secondary-button wire:click="$set('showCrearProductoModal', false)">
                        {{ __('Cancelar') }}
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>
    @endif

    @livewire('modals.crear-zona-modal', ['show' => $showCrearZonaModal, 'ubicacionId' => $currentUbicacionIdForZonaModal])
</div>