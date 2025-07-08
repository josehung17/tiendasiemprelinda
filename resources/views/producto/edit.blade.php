<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            Editar Producto: {{ $producto->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class = "overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 dark-text-gray-100">
                    @can('edit products')
                    {{-- Mostrar errores de validación --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">¡Ups! Hubo algunos problemas con tu entrada.</strong>
                            <ul class="mt-3 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('producto.update', $producto->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Nombre --}}
                        <div>
                            <x-input-label for="nombre" value="Nombre" />
                            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre', $producto->nombre)" required autofocus />
                        </div>

                        {{-- Marcas --}}
                        <div class="mt-4">
                            <x-input-label for="marca_id" value="Marca" />
                            <x-select-input id="marca_id" name="marca_id" class="block mt-1 w-full">
                                <option disabled>Elige una marca</option>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id }}" {{ old('marca_id', $producto->marca_id) == $marca->id ? 'selected' : '' }}>
                                        {{ $marca->nombre }}
                                    </option>
                                @endforeach
                            </x-select-input>
                        </div>

                        {{-- Categorias --}}
                        <div class="mt-4">
                            <x-input-label for="categoria_id" value="Categoría" />
                            <x-select-input id="categoria_id" name="categoria_id" class="block mt-1 w-full">
                                <option disabled>Elige una categoría</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </x-select-input>
                        </div>

                        {{-- Descripción --}}
                        <div class="mt-4">
                            <x-input-label for="descripcion" value="Descripción del Producto" />
                            <x-text-area id="descripcion" name="descripcion" rows="4" class="block p-2.5 w-full" placeholder="Escriba su descripción aquí...">{{ old('descripcion', $producto->descripcion) }}</x-text-area>
                        </div>

                        {{-- Precio --}}
                        <div class="mt-4">
                            <x-input-label for="precio" value="Precio" />
                            <x-text-input id="precio" class="block mt-1 w-full" type="text" name="precio" :value="old('precio', $producto->precio)" required />
                        </div>

                        {{-- Precio con Descuento --}}
                        <div class="mt-4">
                            <x-input-label for="precio_descuento" value="Precio con Descuento (Opcional)" />
                            <x-text-input id="precio_descuento" class="block mt-1 w-full" type="text" name="precio_descuento" :value="old('precio_descuento', $producto->precio_descuento)" />
                        </div>

                        {{-- Imagen --}}
                        <div class="mt-4">
                            <x-input-label for="ruta_imagen" value="Imagen del Producto (Opcional)" />
                            <x-text-input id="ruta_imagen" class="block mt-1 w-full" type="file" name="ruta_imagen" />
                            <div class="mt-2">
                                <x-input-label for="imagen_actual">Imagen Actual:</x-input-label>
                                @if($producto->ruta_imagen)
                                    <img src="{{ asset('storage/' . $producto->ruta_imagen) }}" alt="{{ $producto->nombre }}" class="w-20 h-20 object-cover rounded">
                                @else
                                    <p>No hay imagen asignada.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Checkboxes --}}
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center">
                                <input id="nuevo" name="nuevo" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" @if(old('nuevo', $producto->nuevo)) checked @endif>
                                <label for="nuevo" class="ms-2 text-sm font-medium text-gray-900">Nuevo Producto</label>
                            </div>
                            <div class="flex items-center">
                                <input id="recomendado" name="recomendado" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" @if(old('recomendado', $producto->recomendado)) checked @endif>
                                <label for="recomendado" class="ms-2 text-sm font-medium text-gray-900">Producto Recomendado</label>
                            </div>
                            <div class="flex items-center">
                                <input id="descuento" name="descuento" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" @if(old('descuento', $producto->descuento)) checked @endif>
                                <label for="descuento" class="ms-2 text-sm font-medium text-gray-900">Descuento</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('productos.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancelar
                            </a>
                            <x-primary-button class="ms-4">
                                Actualizar
                            </x-primary-button>
                        </div>
                    </form>
                    @else
                    <p class="text-red-500">No tienes permiso para editar productos.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
