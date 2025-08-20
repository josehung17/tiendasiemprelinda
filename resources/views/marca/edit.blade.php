<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Marca: {{ $marca->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('edit marcas')
                    <form method="POST" action="{{ route('marcas.update', $marca->id) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" required autofocus value="{{ old('nombre', $marca->nombre) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                        </div>

                        <div>
                            <x-input-label for="ruta_imagen" :value="__('Imagen Actual')" />
                            @if ($marca->ruta_imagen)
                                <img src="{{ asset('storage/' . $marca->ruta_imagen) }}" alt="Imagen de {{ $marca->nombre }}" class="mt-2 h-24 w-24 object-cover rounded">
                            @else
                                <p class="mt-2 text-gray-500 dark:text-gray-400">No hay imagen actual.</p>
                            @endif
                            
                            <x-input-label for="ruta_imagen" :value="__('Subir Nueva Imagen (opcional)')" class="mt-4" />
                            <input id="ruta_imagen" name="ruta_imagen" type="file" class="mt-1 block w-full text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-200 dark:hover:file:bg-gray-600" />
                            <x-input-error class="mt-2" :messages="$errors->get('ruta_imagen')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Actualizar') }}</x-primary-button>
                        </div>
                    </form>
                    @else
                    <p class="text-red-500 dark:text-red-400">No tienes permiso para editar marcas.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>