<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            Crear nuevo Producto
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @can('create products')
                    <form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
                        {{-- Directiva CSRF corregida --}}
                        @csrf

                        {{-- Nombre --}}
                        <div>
                            <x-input-label for="nombre" class="block font-medium text-sm text-gray-700">Nombre</x-input-label>
                            <x-text-input id="nombre" type="text" name="nombre" required autofocus class="mt-1 block w-full" />
                        </div>

                        {{-- Marcas --}}
                        <div class="mt-4">
                            <x-input-label for="marca" class="block mb-2">Selecciona una Marca</x-input-label>
                            <x-select-input id="marca" name="marca_id" class="block mt-1 w-full">
                                <option selected disabled>Elige una marca</option>
                                {{-- Bucle foreach corregido --}}
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                @endforeach
                            </x-select-input>
                        </div>

                        {{-- Categorias --}}
                        <div class="mt-4">
                            <x-input-label for="categoria" class="block mb-2">Selecciona una Categoría</x-input-label>
                            <x-select-input id="categoria" name="categoria_id" class="block mt-1 w-full">
                                <option selected disabled>Elige una categoría</option>
                                {{-- Bucle foreach corregido --}}
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </x-select-input>
                        </div>

                        {{-- Descripción --}}
                        <div class="mt-4">
                            <x-input-label for="descripcion" class="block mb-2">Describa el Producto</x-input-label>
                            <x-text-area id="descripcion" name="descripcion" rows="4" class="block p-2.5 w-full" placeholder="Escriba su descripción aquí..."></x-text-area>
                        </div>

                        {{-- Precio --}}
                        <div class="mt-4">
                            <x-input-label for="precio" class="block mb-2">Precio</x-input-label>
                            <x-text-input id="precio" type="text" name="precio" required class="mt-1 block w-full" />
                        </div>

                        {{-- Precio descuento --}}
                        <div class="mt-4">
                            <x-input-label for="precio_descuento" class="block mb-2">Precio con Descuento</x-input-label>
                            <x-text-input id="precio_descuento" type="text" name="precio_descuento" class="mt-1 block w-full" />
                        </div>

                        {{-- Imagen --}}
                        <div class="mt-4">
                            <x-input-label for="ruta_imagen" class="block mb-2">Imagen del Producto</x-input-label>
                            <x-text-input id="ruta_imagen" type="file" name="ruta_imagen" required class="mt-1 block w-full" />
                        </div>

                        {{-- Checkboxes --}}
                        <div class="mt-4 space-y-2">
                            <div>
                                <input id="nuevo" name="nuevo" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500">
                                <label for="nuevo" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Nuevo Producto
                                </label>
                            </div>
                            <div>
                                <input id="recomendado" name="recomendado" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500">
                                <label for="recomendado" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Producto Recomendado
                                </label>
                            </div>
                            <div>
                                <input id="descuento" name="descuento" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500">
                                <label for="descuento" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Descuento
                                </label>
                            </div>
                        </div>



                        <div class="mt-4">
                            <x-primary-button type="submit">Crear</x-primary-button>
                        </div>
                    </form>
                    @else
                    <p class="text-red-500">No tienes permiso para crear productos.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
