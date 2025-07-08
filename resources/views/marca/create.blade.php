<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear nueva marca') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class= overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @can('create marcas')
                    <form method="POST" action="{{ route('marcas.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <x-input-label for="nombre" class="block font-medium text-sm text-gray-700">Nombre</x-input-label>
                            <x-text-input id="nombre" type="text" name="nombre" required autofocus class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="ruta_imagen" class="block font-medium text-sm text-gray-700">Ruta de la imagen</x-input-label>
                            <x-text-input id="ruta_imagen" type="file" name="ruta_imagen" required autofocus class="mt-1 block w-full" />
                        </div>
                        <div class="mt-4">
                            <x-primary-button type="submit">Crear</x-primary-button>
                        </div>
                    </form>
                    @else
                    <p class="text-red-500">No tienes permiso para crear marcas.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>