<div>
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
            Gestionar Movimientos de Stock
        </h2>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
            <form wire:submit.prevent="realizarMovimiento" class="space-y-4">
                <div>
                    <x-input-label for="searchTerm" :value="__('Buscar Producto')" />
                    <input type="text" wire:model.live.debounce.300ms="searchTerm" id="searchTerm" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-400 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100" placeholder="{{ __('Escribe el nombre del producto') }}">

                    @if(!empty($searchTerm) && $this->productSearchResults->count() > 0 && $showSearchResults)
                        <ul class="mt-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 shadow-lg max-h-60 overflow-y-auto">
                            @foreach($this->productSearchResults as $product)
                                <li wire:click="selectProduct({{ $product->id }}, '{{ $product->nombre }}')" class="p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                    {{ $product->nombre }}
                                </li>
                            @endforeach
                        </ul>
                    @elseif(!empty($searchTerm) && $this->productSearchResults->count() == 0 && $showSearchResults)
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No se encontraron productos.') }}</p>
                    @endif

                    
                    <x-input-error class="mt-2" :messages="$errors->get('selectedProductId')" />
                    <x-input-error class="mt-2" :messages="$errors->get('selectedProductName')" />
                </div>

                <div>
                    <x-input-label for="tipoMovimiento" :value="__('Tipo de Movimiento')" />
                    <x-select-input id="tipoMovimiento" wire:model.live="tipoMovimiento" class="mt-1 block w-full">
                        <option value="ajuste-manual">Ajuste Manual</option>
                        <option value="transferencia">Transferencia</option>
                    </x-select-input>
                    <x-input-error class="mt-2" :messages="$errors->get('tipoMovimiento')" />
                </div>

                {{-- Campos para movimientos que no son transferencia --}}
                @if ($tipoMovimiento !== 'transferencia')
                    <div>
                        <x-input-label for="ubicacionAfectadaId" :value="__('Ubicación')" />
                        <x-select-input id="ubicacionAfectadaId" wire:model.live="ubicacionAfectadaId" class="mt-1 block w-full">
                            <option value="">{{ __('Selecciona una ubicación') }}</option>
                            @foreach ($ubicaciones as $ubicacion)
                                <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                            @endforeach
                        </x-select-input>
                        <x-input-error class="mt-2" :messages="$errors->get('ubicacionAfectadaId')" />
                    </div>

                    @if ($ubicacionAfectadaId)
                        <div>
                            <x-input-label for="zonaAfectadaId" :value="__('Zona')" />
                            <x-select-input id="zonaAfectadaId" wire:model="zonaAfectadaId" class="mt-1 block w-full">
                                <option value="">{{ __('Selecciona una zona') }}</option>
                                @foreach ($zonasAfectadas as $zona)
                                    <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('zonaAfectadaId')" />
                        </div>
                    @endif
                @endif

                {{-- Campos para transferencia --}}
                @if ($tipoMovimiento === 'transferencia')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="ubicacionOrigenId" :value="__('Ubicación Origen')" />
                            <x-select-input id="ubicacionOrigenId" wire:model.live="ubicacionOrigenId" class="mt-1 block w-full">
                                <option value="">{{ __('Selecciona ubicación de origen') }}</option>
                                @foreach ($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('ubicacionOrigenId')" />
                        </div>
                        @if ($ubicacionOrigenId)
                            <div>
                                <x-input-label for="zonaOrigenId" :value="__('Zona Origen')" />
                                <x-select-input id="zonaOrigenId" wire:model.live="zonaOrigenId" class="mt-1 block w-full">
                                    <option value="">{{ __('Selecciona zona de origen') }}</option>
                                    @foreach ($zonasOrigen as $zona)
                                        <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error class="mt-2" :messages="$errors->get('zonaOrigenId')" />
                            </div>
                            @if ($zonaOrigenId && $selectedProductId)
                                <p wire:key="stock-origen-{{ $selectedProductId }}-{{ $ubicacionOrigenId }}-{{ $zonaOrigenId }}" class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ __('Stock existente en esta ubicación/zona:') }}
                                    <span class="font-semibold">{{ $stockExistenteOrigen }}</span>
                                </p>
                            @endif
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="ubicacionDestinoId" :value="__('Ubicación Destino')" />
                            <x-select-input id="ubicacionDestinoId" wire:model.live="ubicacionDestinoId" class="mt-1 block w-full">
                                <option value="">{{ __('Selecciona ubicación de destino') }}</option>
                                @foreach ($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('ubicacionDestinoId')" />
                        </div>
                        @if ($ubicacionDestinoId)
                            <div>
                                <x-input-label for="zonaDestinoId" :value="__('Zona Destino')" />
                                <x-select-input id="zonaDestinoId" wire:model.live="zonaDestinoId" class="mt-1 block w-full">
                                    <option value="">{{ __('Selecciona zona de destino') }}</option>
                                    @foreach ($zonasDestino as $zona)
                                        <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error class="mt-2" :messages="$errors->get('zonaDestinoId')" />
                            </div>
                            @if ($zonaDestinoId && $selectedProductId)
                                <p wire:key="stock-destino-{{ $selectedProductId }}-{{ $ubicacionDestinoId }}-{{ $zonaDestinoId }}" class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ __('Stock existente en esta ubicación/zona:') }}
                                    <span class="font-semibold">{{ $stockExistenteDestino }}</span>
                                </p>
                            @endif
                        @endif
                    </div>
                @endif

                <div>
                    <x-input-label for="cantidad" :value="__('Cantidad')" />
                    <x-text-input id="cantidad" wire:model="cantidad" type="number" min="1" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('cantidad')" />
                </div>

                

                

                @if ($tipoMovimiento === 'ajuste-manual')
                    <div>
                        <x-input-label for="motivoAjuste" :value="__('Motivo del Ajuste')" />
                        <x-text-area id="motivoAjuste" wire:model="motivoAjuste" rows="3" class="mt-1 block w-full" />
                        <x-input-error class="mt-2" :messages="$errors->get('motivoAjuste')" />
                    </div>
                @endif

                <x-primary-button>{{ __('Realizar Movimiento') }}</x-primary-button>
            </form>
        </div>

        {{-- Tabla de Movimientos Recientes --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight mb-4">Movimientos Recientes</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ubicación Origen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zona Origen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ubicación Destino</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zona Destino</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Detalles</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($movimientos as $movimiento)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $movimiento->producto->nombre }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ __($movimiento->tipo) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movimiento->cantidad }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movimiento->ubicacionOrigen->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movimiento->zonaOrigen->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movimiento->ubicacionDestino->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movimiento->zonaDestino->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movimiento->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($movimiento->tipo === 'entrada-reposicion')
                                            {{ $movimiento->proveedor->nombre ?? 'N/A' }} ({{ number_format($movimiento->precio_compra_unitario, 2) }})
                                        @elseif($movimiento->tipo === 'salida-venta')
                                            {{ $movimiento->referencia_venta ?? 'N/A' }}
                                        @else
                                            {{ $movimiento->motivo_ajuste ?? 'N/A' }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                        No hay movimientos de stock registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $movimientos->links() }}
                </div>
            </div>
        </div>
    </div>
    </div>