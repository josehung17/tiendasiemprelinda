<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Proveedor: {{ $proveedor->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('edit proveedores')
                    <form method="POST" action="{{ route('proveedores.update', $proveedor->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" required autofocus value="{{ old('nombre', $proveedor->nombre) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $proveedor->email) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="telefono" :value="__('Teléfono')" />
                            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" value="{{ old('telefono', $proveedor->telefono) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                        </div>

                        <div>
                            <x-input-label for="direccion" :value="__('Dirección')" />
                            <x-text-area id="direccion" name="direccion" rows="3" class="mt-1 block w-full">{{ old('direccion', $proveedor->direccion) }}</x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Actualizar') }}</x-primary-button>
                        </div>
                    </form>
                    @else
                    <p class="text-red-500 dark:text-red-400">No tienes permiso para editar proveedores.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
