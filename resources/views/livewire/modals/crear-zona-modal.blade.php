<x-modal name="crear-zona-modal" wire:model="show" focusable wire:ignore.self>
    <form wire:submit.prevent="save" class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Crear Nueva Zona') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Asegúrate de que la zona pertenezca a una ubicación existente.') }}
        </p>

        <div class="mt-6">
            <x-input-label for="ubicacion_id" :value="__('Ubicación')" />
            <x-select-input id="ubicacion_id" wire:model="ubicacion_id" class="mt-1 block w-full">
                <option value="">{{ __('Selecciona una ubicación') }}</option>
                @foreach($ubicaciones as $ubicacion)
                    <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                @endforeach
            </x-select-input>
            <x-input-error class="mt-2" :messages="$errors->get('ubicacion_id')" />
        </div>

        <div class="mt-4">
            <x-input-label for="nombre" :value="__('Nombre de la Zona')" />
            <x-text-input id="nombre" wire:model="nombre" type="text" class="mt-1 block w-full" placeholder="Ej. Pasillo A, Estante 3" />
            <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
        </div>

        <div class="mt-4">
            <x-input-label for="descripcion" :value="__('Descripción (Opcional)')" />
            <x-text-input id="descripcion" wire:model="descripcion" type="text" class="mt-1 block w-full" placeholder="Descripción de la zona" />
            <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button type="button" wire:click="$dispatch('close-crear-zona-modal')">
                {{ __('Cancelar') }}
            </x-secondary-button>

            <x-primary-button class="ms-3">
                {{ __('Guardar Zona') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>