<div>
    <div x-data="{ show: @entangle('showModal') }" x-show="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: none;">
        <div class="fixed inset-0 transform transition-all" x-on:click="show = false">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
        </div>

        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Confirmar Actualización de Precio de Compra
                </h2>

                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    El precio de compra actual del producto es: <strong class="font-semibold">${{ number_format($currentPurchasePrice, 2) }}</strong>.
                </p>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    El nuevo precio de compra ingresado es: <strong class="font-semibold">${{ number_format($newPurchasePrice, 2) }}</strong>.
                </p>
                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    ¿Desea actualizar el precio de compra del producto a <strong class="font-semibold">"${{ number_format($newPurchasePrice, 2) }}"</strong>?
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button wire:click="closeModal">
                        Cancelar
                    </x-secondary-button>

                    <x-primary-button class="ms-3" wire:click="confirmUpdate">
                        Sí, Actualizar
                    </x-primary-button>
                </div>
            </div>
        </div>
    </div>
</div>