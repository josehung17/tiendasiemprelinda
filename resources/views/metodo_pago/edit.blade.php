<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Método de Pago: {{ $metodoPago->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('edit metodos-pago')
                    <form method="POST" action="{{ route('metodos-pago.update', $metodoPago->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" required autofocus value="{{ old('nombre', $metodoPago->nombre) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                        </div>

                        <div>
                            <x-input-label for="divisa" :value="__('Divisa')" />
                            <select id="divisa" name="divisa" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-400 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" required>
                                <option value="Bs" {{ old('divisa', $metodoPago->divisa) == 'Bs' ? 'selected' : '' }}>Bs</option>
                                <option value="$" {{ old('divisa', $metodoPago->divisa) == '$' ? 'selected' : '' }}>$</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('divisa')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Actualizar') }}</x-primary-button>
                        </div>
                    </form>
                    @else
                    <p class="text-red-500 dark:text-red-400">No tienes permiso para editar métodos de pago.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>