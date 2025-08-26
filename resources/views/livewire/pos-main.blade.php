<div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Punto de Venta (POS)</h2>

    {{-- Success/Error Messages --}}
    <div x-data="{ show: false, message: '', type: '' }"
         x-init="$watch('show', value => { if (value) setTimeout(() => show = false, 3000) });
                 $wire.on('success', (msg) => { message = msg; type = 'success'; show = true; });
                 $wire.on('error', (msg) => { message = msg; type = 'error'; show = true; });"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90"
         :class="{ 'bg-green-100 border-green-400 text-green-700': type === 'success', 'bg-red-100 border-red-400 text-red-700': type === 'error' }"
         class="border px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline" x-text="message"></span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="show = false">
            <svg class="fill-current h-6 w-6" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.697l-2.651 3.152a1.2 1.2 0 1 1-1.697-1.697l3.152-2.651-3.152-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.303l2.651-3.152a1.2 1.2 0 1 1 1.697 1.697L11.697 10l3.152 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Client and Product Search --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700">
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Gestión de Clientes</h3>
                @livewire('pos-client-manager')
            </div>

            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700">
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Búsqueda de Productos</h3>
                @livewire('pos-product-manager')
            </div>
        </div>

        {{-- Right Column: Cart and Total --}}
        <div class="lg:col-span-1 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700 flex flex-col">
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Detalle de Venta</h3>

            <div class="flex-grow overflow-y-auto max-h-[500px] mb-4">
                @if(count($cartItems) > 0)
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-600">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cant.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($cartItems as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item['nombre'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">${{ number_format($item['precio'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <input type="number" wire:model.live="cartItems.{{ $index }}.quantity" wire:change="updateCartItemQuantity({{ $index }}, $event.target.value)" min="1" class="w-20 text-center border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">${{ number_format($item['subtotal'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="removeCartItem({{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">Eliminar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-center">El carrito está vacío.</p>
                @endif
            </div>

            <div class="mt-auto pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xl font-semibold text-gray-800 dark:text-gray-200">Total:</span>
                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($this->total, 2) }}</span>
                </div>
                <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Procesar Venta
                </button>
                <button type="button" class="w-full mt-2 inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancelar Venta
                </button>
            </div>
        </div>
    </div>
</div>
