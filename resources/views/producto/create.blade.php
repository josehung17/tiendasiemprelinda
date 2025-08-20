<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Crear nuevo Producto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('create products')
                    <form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" required autofocus value="{{ old('nombre') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                        </div>

                        <div>
                            <x-input-label for="marca_id" :value="__('Marca')" />
                            <x-select-input id="marca_id" name="marca_id" class="mt-1 block w-full" required>
                                <option value="">{{ __('Selecciona una marca') }}</option>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('marca_id')" />
                        </div>

                        <div>
                            <x-input-label for="categoria_id" :value="__('Categoría')" />
                            <x-select-input id="categoria_id" name="categoria_id" class="mt-1 block w-full" required>
                                <option value="">{{ __('Selecciona una categoría') }}</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('categoria_id')" />
                        </div>

                        <div>
                            <x-input-label for="descripcion" :value="__('Descripción')" />
                            <x-text-area id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full" placeholder="{{ __('Escribe la descripción aquí...') }}">{{ old('descripcion') }}</x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                        </div>

                        <div>
                            <x-input-label for="precio" :value="__('Precio')" />
                            <x-text-input id="precio" name="precio" type="number" step="0.01" class="mt-1 block w-full" required value="{{ old('precio') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('precio')" />
                        </div>

                        <div>
                            <x-input-label for="precio_descuento" :value="__('Precio con Descuento (opcional)')" />
                            <x-text-input id="precio_descuento" name="precio_descuento" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('precio_descuento') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('precio_descuento')" />
                        </div>

                        <div>
                            <x-input-label for="ruta_imagen" :value="__('Imagen del Producto')" />
                            <input id="ruta_imagen" name="ruta_imagen" type="file" class="mt-1 block w-full text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-200 dark:hover:file:bg-gray-600" required />
                            <x-input-error class="mt-2" :messages="$errors->get('ruta_imagen')" />
                        </div>

                        <div class="mt-4 space-y-2">
                            <div class="flex items-center">
                                <input id="nuevo" name="nuevo" type="checkbox" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('nuevo') ? 'checked' : '' }}>
                                <label for="nuevo" class="ms-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ __('Nuevo Producto') }}
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="recomendado" name="recomendado" type="checkbox" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('recomendado') ? 'checked' : '' }}>
                                <label for="recomendado" class="ms-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ __('Producto Recomendado') }}
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="descuento" name="descuento" type="checkbox" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('descuento') ? 'checked' : '' }}>
                                <label for="descuento" class="ms-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ __('Con Descuento') }}
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Crear') }}</x-primary-button>
                        </div>
                    </form>
                    @else
                    <p class="text-red-500 dark:text-red-400">No tienes permiso para crear productos.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>