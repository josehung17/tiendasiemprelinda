<div class="h-screen bg-gray-100 dark:bg-gray-900">

    @if($locationIsSelected)
        {{-- Main POS Interface --}}
        <div class="flex flex-col lg:flex-row h-full">
            {{-- Left Column: Product Search & Client Management --}}
            <div class="w-full lg:w-1/3 px-4 pt-0 flex flex-col space-y-4">
                {{-- Store Selector --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 mt-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Ubicación Actual:</span>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $activeLocationName }}</h3>
                        </div>
                        <button wire:click="clearLocation" class="px-3 py-1 border border-red-300 dark:border-red-700 rounded-md text-sm font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150">
                            Cambiar
                        </button>
                    </div>
                </div>

                {{-- Client Management --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-1">
                    @livewire('pos-client-manager')
                </div>

                {{-- Product Search --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-0 flex-grow">
                    @livewire('pos-product-manager')
                </div>
            </div>

            {{-- Right Column: Sales Detail (Cart & Checkout) --}}
            <div class="w-full lg:w-2/3 p-4 flex flex-col space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 flex-grow flex flex-col">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Detalle de Venta</h3>

                    {{-- Cart Items --}}
                    <div class="flex-grow overflow-y-auto mb-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                        @if(count($cartItems) > 0)
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cant.</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                        <th scope="col" class="relative px-3 py-2"><span class="sr-only">Acciones</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($cartItems as $index => $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td wire:click="openProductDetailModal({{ $item['product_id'] }})" class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center space-x-2 cursor-pointer">
                                                @if($item['ruta_imagen'])
                                                    <img src="{{ asset('storage/' . $item['ruta_imagen']) }}" alt="{{ $item['nombre'] }}" class="w-8 h-8 object-cover rounded">
                                                @else
                                                    <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded">
                                                        <span class="text-gray-500 dark:text-gray-400 text-xs">No Img</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    {{ $item['nombre'] }}<br>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        Zona: {{ $item['zone_name'] }}
                                                    </span>
                                                    <br>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    @if($showPricesInBolivar && $bolivarExchangeRate !== null)
                                                        Bs. {{ number_format($item['precio'] * $bolivarExchangeRate, 2) }} c/u
                                                    @else
                                                        ${{ number_format($item['precio'], 2) }} c/u
                                                    @endif
                                                </span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                <input type="number" wire:model.live="cartItems.{{ $index }}.quantity" wire:change="updateCartItemQuantity({{ $index }}, $event.target.value)" min="1" class="w-20 text-center border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                @if($showPricesInBolivar && $bolivarExchangeRate !== null)
                                                    Bs. {{ number_format($item['subtotal'] * $bolivarExchangeRate, 2) }}
                                                @else
                                                    ${{ number_format($item['subtotal'], 2) }}
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                                <button wire:click="removeCartItem({{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 text-xl">
                                                    &times;
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">El carrito está vacío.</p>
                        @endif
                    </div>

                    {{-- Totals and Action Buttons --}}
                    <div class="mt-auto pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xl font-semibold text-gray-800 dark:text-gray-200">Total:</span>
                            <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                @if($showPricesInBolivar && $bolivarExchangeRate !== null)
                                    Bs. {{ number_format($this->bolivarTotal, 2) }}
                                @else
                                    ${{ number_format($this->total, 2) }}
                                @endif
                            </span>
                        </div>

                        @if($bolivarExchangeRate !== null)
                            <div class="flex justify-end mb-4 space-x-2">
                                <button wire:click="toggleCurrencyDisplay" class="px-3 py-1 border border-gray-300 dark:border-gray-700 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                    @if($showPricesInBolivar)
                                        Mostrar en $
                                    @else
                                        Mostrar en Bs.
                                    @endif
                                </button>
                                <button wire:click="fetchBolivarExchangeRate" class="px-3 py-1 border border-gray-300 dark:border-gray-700 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                    Actualizar Tasa
                                </button>
                            </div>
                        @endif

                        <button type="button" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                            Procesar Venta
                        </button>
                        <button type="button" class="w-full mt-3 inline-flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                            Cancelar Venta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Location Selection Modal --}}
        <div class="fixed inset-0 bg-gray-700 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8 w-full max-w-md mx-auto">
                <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-200 mb-6">Seleccione una Tienda</h2>
                <div class="space-y-4">
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tienda / Ubicación</label>
                        <select wire:model.defer="selectedLocationToSet" id="location" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">-- Seleccione una opción --</option>
                            @foreach($availableLocations as $location)
                                <option value="{{ $location->id }}">{{ $location->nombre }}</option>
                            @endforeach
                        </select>
                        @error('selectedLocationToSet') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button wire:click="setLocation" wire:loading.attr="disabled" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        <span wire:loading.remove wire:target="setLocation">Confirmar Tienda</span>
                        <span wire:loading wire:target="setLocation">Confirmando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showProductDetailModal)
        @livewire('product-modal', ['productId' => $selectedProductIdForDetail], key($selectedProductIdForDetail . '-' . now()->timestamp))
    @endif
</div>