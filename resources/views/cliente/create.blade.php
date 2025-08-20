<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Crear nuevo Cliente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('create clients')
                    <form method="POST" action="{{ route('clientes.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="tipo_documento" :value="__('Tipo de Documento')" />
                            <x-select-input id="tipo_documento" name="tipo_documento" class="mt-1 block w-full" required>
                                <option value="">{{ __('Selecciona un tipo') }}</option>
                                @foreach ($tiposDocumento as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo_documento') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('tipo_documento')" />
                        </div>

                        <div>
                            <x-input-label for="nombre" :value="__('Nombre Completo')" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" required autofocus value="{{ old('nombre') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                        </div>

                        <div>
                            <x-input-label for="numero_documento" :value="__('Número de Documento')" />
                            <x-text-input id="numero_documento" name="numero_documento" type="text" class="mt-1 block w-full" required value="{{ old('numero_documento') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('numero_documento')" />
                        </div>

                        <div>
                            <x-input-label for="telefono" :value="__('Teléfono (Opcional)')" />
                            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" value="{{ old('telefono') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email (Opcional)')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="direccion" :value="__('Dirección (Opcional)')" />
                            <x-text-area id="direccion" name="direccion" rows="3" class="mt-1 block w-full">{{ old('direccion') }}</x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Crear') }}</x-primary-button>
                        </div>
                    </form>
                    @else
                    <p class="text-red-500 dark:text-red-400">No tienes permiso para crear clientes.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
