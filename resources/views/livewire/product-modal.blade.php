<div>
    <x-modal name="product-view-modal" wire:model="show" maxWidth="lg" @click.away="$wire.closeModal()">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Detalles del Producto
            </h2>

            @if ($product)
                <div class="mt-4 space-y-4">
                    {{-- Imagen --}}
                    @if ($product->ruta_imagen)
                        <div>
                            <img src="{{ asset('storage/' . $product->ruta_imagen) }}" alt="Imagen de {{ $product->nombre }}" class="w-full h-64 object-contain rounded-lg">
                        </div>
                    @endif

                    {{-- Indicadores --}}
                    <div class="flex items-center space-x-2">
                        @if($product->nuevo)
                            <span class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full">Nuevo</span>
                        @endif
                        @if($product->recomendado)
                            <span class="px-2 py-1 text-xs font-semibold text-white bg-green-500 rounded-full">Recomendado</span>
                        @endif
                        @if($product->descuento)
                            <span class="px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded-full">Con Descuento</span>
                        @endif
                    </div>

                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nombre:</strong>
                        <p class="mt-1 text-lg text-gray-900 dark:text-gray-100 font-semibold">{{ $product->nombre }}</p>
                    </div>
                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Descripción:</strong>
                        <p class="mt-1 text-gray-800 dark:text-gray-200">{{ $product->descripcion }}</p>
                    </div>
                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Precio:</strong>
                        @if($product->descuento && $product->precio_descuento)
                            <div class="flex items-center space-x-2 mt-1">
                                <p class="text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($product->precio_descuento, 2) }}</p>
                                <p class="text-lg text-gray-500 dark:text-gray-400 line-through">${{ number_format($product->precio, 2) }}</p>
                            </div>
                        @else
                            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($product->precio, 2) }}</p>
                        @endif
                    </div>
                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Categoría:</strong>
                        <p class="mt-1 text-gray-800 dark:text-gray-200">{{ $product->categoria->nombre }}</p>
                    </div>
                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Marca:</strong>
                        <p class="mt-1 text-gray-800 dark:text-gray-200">{{ $product->marca->nombre }}</p>
                    </div>
                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Stock por Ubicación:</strong>
                        @if($stockLocations->isNotEmpty())
                            <ul class="mt-1 space-y-1 text-gray-800 dark:text-gray-200">
                                @foreach($stockLocations as $location)
                                    <li>
                                        {{ $location->ubicacion_nombre }}
                                        @if($location->zona_nombre)
                                            ({{ $location->zona_nombre }})
                                        @endif
                                        : <span class="font-semibold">{{ $location->stock }}</span> unidades
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-1 text-gray-500 dark:text-gray-400">No hay stock disponible en ninguna ubicación.</p>
                        @endif
                    </div>
                    <div>
                        <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Margen de Ganancia:</strong>
                        <p class="mt-1 text-gray-800 dark:text-gray-200">{{ $product->margen_ganancia }}%</p>
                    </div>
                </div>
            @else
                <p class="mt-4 text-gray-500 dark:text-gray-400">Cargando producto...</p>
            @endif

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="closeModal">
                    Cerrar
                </x-secondary-button>
            </div>
        </div>
    </x-modal>
</div>
