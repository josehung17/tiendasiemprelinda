<x-modal wire:model="show">
    @if ($producto)
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Gestionar Stock Mínimo para:') }} {{ $producto->nombre }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Ubicación:') }} {{ App\Models\Ubicacion::find($ubicacionId)->nombre ?? '' }}
            </p>

            <div class="mt-6 space-y-4">
                @forelse ($zonas as $zona)
                    <div class="grid grid-cols-3 items-center gap-4 p-2 rounded-lg bg-gray-100 dark:bg-gray-700/50">
                        <div class="col-span-1">
                            <label for="stock_minimo_{{ $zona->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $zona->nombre }}
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Stock Actual: {{ $zona->pivot->stock }}</p>
                        </div>
                        <div class="col-span-2">
                            <x-text-input 
                                id="stock_minimo_{{ $zona->id }}" 
                                type="number" 
                                class="mt-1 block w-full"
                                wire:model="stocksMinimos.{{ $zona->id }}"
                                min="0"
                                placeholder="{{ __('Stock Mínimo') }}"
                            />
                        </div>
                    </div>
                @empty
                    <p>{{ __('No hay zonas con stock para este producto en la ubicación seleccionada.') }}</p>
                @endforelse
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="cerrarModal">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" wire:click="save">
                    {{ __('Guardar Cambios') }}
                </x-primary-button>
            </div>
        </div>
    @endif
</x-modal-lg>