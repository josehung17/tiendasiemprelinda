<div>
    <x-modal name="confirm-purchase-price-modal" :show="$showModal" maxWidth="md">
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
                ¿Desea actualizar el precio de compra del producto a <strong class="font-semibold">${{ number_format($newPurchasePrice, 2) }}</strong>?
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
    </x-modal>
</div>