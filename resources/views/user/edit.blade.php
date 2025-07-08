<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Usuario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('edit users')
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        
                       <div>
                            <x-input-label for="name" class="block font-medium text-sm text-gray-700">Nombre</x-input-label>
                            <x-text-input id="name" type="text" name="name" value="{{ $user->name }}" required autofocus class="mt-1 block w-full" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="email" class="block font-medium text-sm text-gray-700">Email</x-input-label>
                            <x-text-input id="email" type="email" name="email" value="{{ $user->email }}" required class="mt-1 block w-full" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="password" class="block font-medium text-sm text-gray-700">Nueva Contraseña (opcional)</x-input-label>
                            <x-text-input id="password" type="password" name="password" class="mt-1 block w-full" />
                        </div>
                        
                        <div class="mt-4">
                            <x-primary-button type="submit">Actualizar</x-primary-button>
                        </div>
                    </form>
                    @else
                    <p class="text-red-500">No tienes permiso para editar usuarios.</p>
                    @endcan
                </div>
            </div>
        </div>      
    </div>
</x-app-layout>