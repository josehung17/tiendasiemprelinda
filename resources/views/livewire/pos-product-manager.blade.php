<div x-data="{ searchTerm: '', products: @js($products), isImageModalOpen: false, modalImageUrl: '' }" class="h-full flex flex-col">
    <div class="mb-4 px-4 pt-4">
        <label for="productSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar Producto por Nombre</label>
        <input type="text" x-model.debounce.300ms="searchTerm" id="productSearch" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-400 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 dark:focus:ring-opacity-50 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500" placeholder="Escribe 2 o más caracteres...">
    </div>

    <div class="flex-grow overflow-y-auto">
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            <template x-if="searchTerm.length >= 2">
                <template x-for="product in products.filter(p => p.nombre.toLowerCase().includes(searchTerm.toLowerCase()))" :key="product.id">
                    <li @click="$store.cart.add(product); searchTerm = ''" 
                        class="p-3 flex items-center space-x-3 cursor-pointer hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors duration-150">
                        
                        <template x-if="product.ruta_imagen">
                            <img @click.stop="isImageModalOpen = true; modalImageUrl = `{{ asset('storage') }}/${product.ruta_imagen}`" 
                                 :src="`{{ asset('storage') }}/${product.ruta_imagen}`" 
                                 :alt="product.nombre" 
                                 class="w-12 h-12 object-cover rounded cursor-pointer hover:opacity-75 transition">
                        </template>
                        <template x-if="!product.ruta_imagen">
                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 flex items-center justify-center rounded text-center">
                                <span class="text-gray-500 dark:text-gray-400 text-xs">Sin<br>Img</span>
                            </div>
                        </template>
                        
                        <div class="flex-grow">
                            <p class="font-semibold text-sm text-gray-900 dark:text-gray-100" x-text="product.nombre"></p>
                            <p class="text-gray-700 dark:text-gray-300 text-xs">
                                <span>Precio: <span x-text="`${parseFloat(product.precio).toFixed(2)}`"></span></span> |
                                <span>Stock: <span class="font-bold" x-text="product.stock_display"></span> (<span x-text="product.zone_display_name"></span>)</span>
                            </p>
                        </div>
                        <div class="text-indigo-600 dark:text-indigo-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </li>
                </template>
            </template>

            <template x-if="searchTerm.length >= 2 && products.filter(p => p.nombre.toLowerCase().includes(searchTerm.toLowerCase())).length === 0">
                <li class="p-4 text-center text-gray-500 dark:text-gray-400">
                    No se encontraron productos con ese nombre.
                </li>
            </template>
        </ul>
    </div>

    <!-- Image Modal -->
    <div x-show="isImageModalOpen" 
         @keydown.escape.window="isImageModalOpen = false" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" 
         style="display: none;">
        <div @click.away="isImageModalOpen = false" class="relative p-4">
            <button @click="isImageModalOpen = false" class="absolute -top-2 -right-2 text-white bg-gray-800 rounded-full p-1 hover:bg-gray-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <img :src="modalImageUrl" class="object-contain max-w-3xl max-h-[80vh] rounded">
        </div>
    </div>
</div>