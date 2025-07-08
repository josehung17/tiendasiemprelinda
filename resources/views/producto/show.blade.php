<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalles del Producto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Columna de la Imagen --}}
                        <div class="md:col-span-1">
                            @if($producto->ruta_imagen)
                                <img src="{{ asset('storage/' . $producto->ruta_imagen) }}" alt="Imagen de {{ $producto->nombre }}" class="w-full h-auto object-cover rounded-lg shadow-md">
                            @else
                                <div class="w-full h-80 bg-gray-200 flex items-center justify-center rounded-lg">
                                    <span class="text-gray-500">Sin imagen</span>
                                </div>
                            @endif
                        </div>

                        {{-- Columna de Detalles --}}
                        <div class="md:col-span-2 space-y-4">
                            {{-- Nombre --}}
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Nombre del Producto</h3>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $producto->nombre }}</p>
                            </div>

                            {{-- DescripciÃ³n --}}
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">DescripciÃ³n</h3>
                                <p class="mt-1 text-base text-gray-700">{{ $producto->descripcion }}</p>
                            </div>

                            {{-- Precios --}}
                            <div class="flex items-baseline space-x-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Precio Regular</h3>
                                    <p class="mt-1 text-xl text-gray-800">${{ number_format($producto->precio, 2) }}</p>
                                </div>
                                @if($producto->precio_promo)
                                <div class="text-red-600">
                                    <h3 class="text-sm font-medium">Precio Promocional</h3>
                                    <p class="mt-1 text-xl font-bold">${{ number_format($producto->precio_promo, 2) }}</p>
                                </div>
                                @endif
                            </div>

                            {{-- CategorÃ­a y Marca --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">CategorÃ­a</h3>
                                    <p class="mt-1 text-base text-gray-800">{{ $producto->categoria->nombre }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Marca</h3>
                                    <p class="mt-1 text-base text-gray-800">{{ $producto->marca->nombre }}</p>
                                </div>
                            </div>

                            {{-- Indicadores (Badges) --}}
                            <div class="flex items-center space-x-2 pt-2">
                                @if($producto->nuevo)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Nuevo</span>
                                @endif
                                @if($producto->recomendado)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Recomendado</span>
                                @endif
                                @if($producto->descuento)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">En Descuento</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Botones de Acción --}}
                    <div class="mt-6 border-t border-gray-200 pt-4 flex items-center justify-end space-x-4">
                        <a href="{{ route('productos.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900">Volver a la lista</a>
                        <a href="{{ route('productos.edit', $producto->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Editar Producto
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>