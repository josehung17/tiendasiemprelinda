<div>
    <x-modal name="crear-marca-modal" wire:model="show" maxWidth="lg">
        <form wire:submit.prevent="store" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Crear Nueva Marca
            </h2>

            <div class="mt-6">
                <x-input-label for="nombre" :value="__('Nombre')" />
                <x-text-input id="nombre" wire:model.defer="nombre" type="text" class="mt-1 block w-full" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('show', false)">
                    Cancelar
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    Guardar
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
