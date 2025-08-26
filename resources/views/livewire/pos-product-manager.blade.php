<div>
    <div class="mb-4">
        <label for="productSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar Producto por Nombre</label>
        <input type="text" wire:model.live.debounce.300ms="searchTerm" id="productSearch" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-400 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" placeholder="Nombre del Producto">
    </div>

    @if(!empty($searchTerm) && $this->searchResults->count() > 0)
        <ul class="mt-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 shadow-lg max-h-60 overflow-y-auto">
            @foreach($this->searchResults as $product)
                <li wire:click="addProductToCart({{ $product->id }})" class="p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 last:border-b-0 flex items-center space-x-3">
                    @if($product->ruta_imagen)
                        <img src="{{ asset('storage/' . $product->ruta_imagen) }}" alt="{{ $product->nombre }}" class="w-12 h-12 object-cover rounded">
                    @else
                        <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded">
                            <span class="text-gray-500 dark:text-gray-400 text-xs">No Img</span>
                        </div>
                    @endif
                    <div class="flex-grow">
                        <p class="font-semibold text-sm text-gray-900 dark:text-gray-100">{{ $product->nombre }}</p>
                        <p class="text-gray-700 dark:text-gray-300 text-xs">Precio: ${{ number_format($product->precio, 2) }} | Stock: {{ $product->stock }}</p>
                    </div>
                    <input type="number" wire:model.live="quantities.{{ $product->id }}" min="1" value="1" class="w-16 text-center border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                </li>
            @endforeach
        </ul>
    @elseif(!empty($searchTerm) && $this->searchResults->count() == 0)
        <p class="mt-4 text-gray-500 dark:text-gray-400 text-center">No se encontraron productos.</p>
    @endif
</div>
