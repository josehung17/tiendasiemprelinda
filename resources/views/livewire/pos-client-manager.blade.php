<div>
    <div x-data="{ isMinimized: @entangle('isMinimized') }">
        <div x-show="!isMinimized">
            <div class="flex justify-between items-center mb-1">
                <label for="clientSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar Cliente (DNI o Nombre)</label>
                <button @click="isMinimized = !isMinimized; $wire.isMinimized = isMinimized" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
                    <template x-if="isMinimized">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </template>
                    <template x-if="!isMinimized">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    </template>
                </button>
            </div>
            <div class="mt-1 flex rounded-md shadow-sm">
                <input type="text" wire:model.live.debounce.300ms="searchTerm" id="clientSearch" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-400 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 dark:focus:ring-opacity-50" placeholder="DNI o Nombre del Cliente">
            </div>
        </div>

        @if(!empty($searchTerm) && $this->searchResults && count($this->searchResults) > 0)
            <ul class="mt-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 shadow-lg max-h-60 overflow-y-auto">
                @foreach($this->searchResults as $client)
                    <li wire:click="selectClient({{ $client->id }})" class="p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                        {{ $client->nombre }} ({{ $client->numero_documento }})
                    </li>
                @endforeach
            </ul>
        @elseif(!empty($searchTerm) && $this->searchResults && count($this->searchResults) == 0)
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No se encontraron clientes.</p>
        @endif

        <div x-show="isMinimized" @click="isMinimized = !isMinimized; $wire.isMinimized = isMinimized" class="cursor-pointer">
            @if($selectedClient)
                <p class="text-sm font-semibold my-0">Cliente: {{ $selectedClient->nombre }} ({{ $selectedClient->numero_documento }})</p>
            @else
                <p class="text-sm font-semibold my-0">Cliente: Consumidor Final</p>
            @endif
        </div>

        <div x-show="!isMinimized">
            <div class="mb-1">
                @if($selectedClient)
                    <p class="text-base font-semibold">Cliente Seleccionado:</p>
                    <p class="text-md">{{ $selectedClient->nombre }} ({{ $selectedClient->numero_documento }})</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedClient->telefono }}</p>
                @else
                    <p class="text-base font-semibold">Cliente: Consumidor Final</p>
                @endif
            </div>

            <div class="mb-1">
                <button type="button" wire:click="openCreateClientModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    + Nuevo Cliente
                </button>
            </div>
        </div>
    </div>

    <x-modal wire:model="showCreateClientModal" name="create-client-modal">
        @livewire('modals.create-client-modal')
    </x-modal>
</div>
