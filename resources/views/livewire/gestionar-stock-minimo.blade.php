<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestionar Stock Mínimo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Dropdown para seleccionar ubicación -->
                    <div class="mb-4">
                        <x-input-label for="ubicacion" :value="__('Selecciona una Ubicación')" />
                        <x-select-input id="ubicacion" wire:model.live="selectedUbicacionId" class="mt-1 block w-full md:w-1/3">
                            <option value="">{{ __('---') }}</option>
                            @foreach ($ubicaciones as $ubicacion)
                                <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                            @endforeach
                        </x-select-input>
                    </div>

                    @if ($selectedUbicacionId)
                        <!-- Tabla de productos -->
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">{{ __('Producto') }}</th>
                                        <th scope="col" class="px-6 py-3">{{ __('Stock Actual Total') }}</th>
                                        <th scope="col" class="px-6 py-3">{{ __('Stock Mínimo Total') }}</th>
                                        <th scope="col" class="px-6 py-3"><span class="sr-only">{{ __('Acciones') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($productos as $producto)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $producto->nombre }}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ $producto->ubicaciones->sum('pivot.stock') }}
                                            </td>
                                            <td class="px-6 py-4 @if($producto->ubicaciones->sum('pivot.stock') <= $producto->ubicaciones->sum('pivot.stock_minimo') && $producto->ubicaciones->sum('pivot.stock_minimo') > 0) text-red-500 font-bold @endif">
                                                {{ $producto->ubicaciones->sum('pivot.stock_minimo') }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <x-primary-button wire:click="abrirModalStockMinimo({{ $producto->id }})">
                                                    {{ __('Gestionar') }}
                                                </x-primary-button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td colspan="4" class="px-6 py-4 text-center">
                                                {{ __('No hay productos asignados a esta ubicación.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Inclusión del Modal -->
    @livewire('modals.stock-minimo-modal')
</div>