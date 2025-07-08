<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Marca
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('edit marcas')
                    <form method="POST" action="{{ route('marcas.update', $marca->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <x-input-label for="nombre" class="block font-medium text-sm text-gray-700">Nombre</x-input-label>
                            <x-text-input id="nombre" type="text" name="nombre" value="{{ $marca->nombre }}" required autofocus class="mt-1 block w-full" />
                        </div>
                        
                        <div>
                            <x-input-label for="ruta_imagen" class="block font-medium text-sm text-gray-700">Ruta de la imagen</x-input-label>
                            <x-text-input id="ruta_imagen" type="file" name="ruta_imagen" required autofocus class="mt-1 block w-full" />
                        </div>
                        
                        <div class="mt-4">
                            <x-primary-button type="submit">Actualizar</x-primary-button>
                        </div>
                    </form>
                    @else
                    <p class="text-red-500">No tienes permiso para editar marcas.</p>
                    @endcan
                </div>
            </div>
        </div>      
 
    </div>
</x-app-layout>
