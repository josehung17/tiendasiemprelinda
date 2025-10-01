<div x-data="{ showPricesInBolivar: false, activeExchangeRate: {{ $activeExchangeRate ? $activeExchangeRate->tasa : 'null' }}, isDetailModalOpen: false, detailModalProduct: null }" x-init="$store.cart.init()" @clear-cart.window="$store.cart.clear()" @venta-procesada.window="$store.cart.clear()" class="h-screen bg-gray-100 dark:bg-gray-900">

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
                            @if($activeExchangeRate)
                                <span class="text-xs text-gray-500 dark:text-gray-400">Tasa de cambio: {{ $activeExchangeRate->tasa }} (vigente desde {{ \Carbon\Carbon::parse($activeExchangeRate->fecha_vigencia)->format('d/m/Y') }})</span>
                            @endif
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
                        <template x-if="$store.cart.items.length > 0">
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
                                    <template x-for="item in $store.cart.items" :key="`${item.product_id}-${item.zone_id}`">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td @click="detailModalProduct = item; isDetailModalOpen = true" class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center space-x-2 cursor-pointer">
                                                <template x-if="item.ruta_imagen">
                                                    <img :src="`{{ asset('storage') }}/${item.ruta_imagen}`" :alt="item.nombre" class="w-8 h-8 object-cover rounded">
                                                </template>
                                                <template x-if="!item.ruta_imagen">
                                                    <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded">
                                                        <span class="text-gray-500 dark:text-gray-400 text-xs">No Img</span>
                                                    </div>
                                                </template>
                                                <div>
                                                    <span x-text="item.nombre"></span><br>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="`Zona: ${item.zone_name}`"></span><br>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        <span x-show="!showPricesInBolivar" x-text="`$${parseFloat(item.precio).toFixed(2)} c/u`"></span>
                                                        <span x-show="showPricesInBolivar && activeExchangeRate" x-text="`Bs. ${(item.precio * activeExchangeRate).toFixed(2)} c/u`"></span>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                <input type="number" :value="item.quantity" @change="$store.cart.updateQuantity(item.product_id, item.zone_id, $event.target.value)" min="1" :max="item.stock_disponible" class="w-20 text-center border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                <span x-show="!showPricesInBolivar" x-text="`$${(item.precio * item.quantity).toFixed(2)}`"></span>
                                                <span x-show="showPricesInBolivar && activeExchangeRate" x-text="`Bs. ${(item.precio * item.quantity * activeExchangeRate).toFixed(2)}`"></span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                                <button @click="$store.cart.remove(item.id, item.zone_display_id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 text-xl">&times;</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </template>
                        <template x-if="$store.cart.items.length === 0">
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">El carrito está vacío.</p>
                        </template>
                    </div>

                    {{-- Totals and Action Buttons --}}
                    <div class="mt-auto pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xl font-semibold text-gray-800 dark:text-gray-200">Total:</span>
                            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                <span x-show="!showPricesInBolivar" x-text="`$${$store.cart.total}`"></span>
                                <span x-show="showPricesInBolivar && activeExchangeRate" x-text="`Bs. ${($store.cart.total * activeExchangeRate).toFixed(2)}`"></span>
                            </div>
                        </div>

                        @if($activeExchangeRate)
                            <div class="flex justify-end mb-4 space-x-2">
                                <button @click="showPricesInBolivar = !showPricesInBolivar" class="px-3 py-1 border border-gray-300 dark:border-gray-700 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                    <span x-show="!showPricesInBolivar">Mostrar en Bs.</span>
                                    <span x-show="showPricesInBolivar">Mostrar en $</span>
                                </button>
                            </div>
                        @endif

                        <button @click="$wire.procesarVenta(JSON.stringify($store.cart.items))" :disabled="$store.cart.items.length === 0 || !selectedClient" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                            Procesar Venta
                        </button>
                        <button @click="$store.cart.clear()" type="button" class="w-full mt-3 inline-flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                            Cancelar Venta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Location and Rate Selection Modal --}}
        <div class="fixed inset-0 bg-gray-700 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-auto">
                
                {{-- Step 1: Location Selection --}}
                @if($modalStep === 'location')
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-200 mb-6">Seleccione una Tienda</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tienda / Ubicación</label>
                            <select wire:model="selectedLocationToSet" id="location" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
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
                @endif

                {{-- Other Modal Steps --}}
                {{-- ... (kept same as before) ... --}}

            </div>
        </div>
    @endif

    {{-- NEW Alpine Product Detail Modal --}}
    <div x-show="isDetailModalOpen" @keydown.escape.window="isDetailModalOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" style="display: none;">
        <div @click.away="isDetailModalOpen = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-auto">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" x-text="detailModalProduct?.nombre || 'Detalles del Producto'"></h2>
                    <button @click="isDetailModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                
                <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2" x-show="detailModalProduct">
                    <template x-if="detailModalProduct?.ruta_imagen">
                        <img :src="`{{ asset('storage') }}/${detailModalProduct.ruta_imagen}`" :alt="detailModalProduct.nombre" class="w-full h-64 object-contain rounded-lg bg-gray-100 dark:bg-gray-700">
                    </template>

                    <div class="flex items-center space-x-2">
                        <template x-if="detailModalProduct?.nuevo"><span class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full">Nuevo</span></template>
                        <template x-if="detailModalProduct?.recomendado"><span class="px-2 py-1 text-xs font-semibold text-white bg-green-500 rounded-full">Recomendado</span></template>
                        <template x-if="detailModalProduct?.descuento"><span class="px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded-full">Con Descuento</span></template>
                    </div>

                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Descripción:</strong>
                        <p class="mt-1 text-gray-800 dark:text-gray-200" x-text="detailModalProduct?.descripcion"></p>
                    </div>
                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Precio:</strong>
                        <div x-show="detailModalProduct?.descuento && detailModalProduct?.precio_descuento" class="flex items-center space-x-2 mt-1" style="display: none;">
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400" x-text="`$${parseFloat(detailModalProduct?.precio_descuento).toFixed(2)}`"></p>
                            <p class="text-lg text-gray-500 dark:text-gray-400 line-through" x-text="`$${parseFloat(detailModalProduct?.precio).toFixed(2)}`"></p>
                        </div>
                        <div x-show="!detailModalProduct?.descuento || !detailModalProduct?.precio_descuento">
                            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="`$${parseFloat(detailModalProduct?.precio).toFixed(2)}`"></p>
                        </div>
                    </div>
                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Categoría:</strong>
                        <p class="mt-1 text-gray-800 dark:text-gray-200" x-text="detailModalProduct?.categoria?.nombre"></p>
                    </div>
                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Marca:</strong>
                        <p class="mt-1 text-gray-800 dark:text-gray-200" x-text="detailModalProduct?.marca?.nombre"></p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 mt-6 flex justify-end">
                <button @click="isDetailModalOpen = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">Cerrar</button>
            </div>
        </div>
    </div>

</div>
