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
                        @livewire('gestionar-creacion-producto', ['marcas' => $marcas, 'categorias' => $categorias])
                    @else
                        <p class="text-red-500 dark:text-red-400">No tienes permiso para crear productos.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>