<div class="p-4 sm:p-6 lg:p-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Configuración de Zona Predeterminada para POS</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Asigna la zona de la cual se descontará el stock por defecto al vender un producto en el POS.</p>
        </div>
    </div>

    @if(Auth::user()->hasRole('admin'))
        <div class="mt-4">
            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selecciona una tienda para configurar:</label>
            <select wire:model.live="selectedUbicacionId" id="location" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">-- Seleccionar --</option>
                @foreach($ubicacionesDisponibles as $ubicacion)
                    <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="mt-8">
        @if($selectedUbicacionId)
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Configurando productos para: <span class="font-bold text-indigo-600">{{ $ubicacionActivaNombre }}</span></h2>
            
            <div class="mt-4 -my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200 sm:pl-6">Producto</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">Zonas en esta Ubicación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @forelse ($productos as $producto)
                                    <tr>
                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-200 sm:pl-6">{{ $producto->nombre }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <div class="space-y-2">
                                                @foreach($allZonesForSelectedUbicacion as $zona)
                                                    @php
                                                        $pivot = $producto->zonasStock->firstWhere('id', $zona->id)->pivot ?? null;
                                                        $stock = $pivot ? $pivot->stock : 0;
                                                        $isPredeterminada = $pivot ? $pivot->es_zona_predeterminada_pos : false;
                                                    @endphp
                                                    <div class="flex items-center justify-between">
                                                        <span>- {{ $zona->nombre }} (Stock: {{ $stock }})</span>
                                                        <div>
                                                            @if($isPredeterminada)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                                    Predeterminada
                                                                </span>
                                                            @else
                                                                <button wire:click="setPredeterminada({{ $producto->id }}, {{ $zona->id }})" wire:loading.attr="disabled" class="text-xs text-indigo-600 hover:text-indigo-900 disabled:opacity-50">
                                                                    Establecer como predeterminada
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                            No hay productos con stock en esta ubicación.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @else
            @if(!Auth::user()->hasRole('admin'))
                <div class="text-center py-12">
                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Para empezar, por favor selecciona una tienda en el módulo principal del POS.</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Una vez seleccionada, podrás configurar los productos de esa tienda aquí.</p>
                </div>
            @else
                 <div class="text-center py-12">
                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Por favor, selecciona una tienda del menú de arriba para ver su configuración.</p>
                </div>
            @endif
        @endif
    </div>
</div>