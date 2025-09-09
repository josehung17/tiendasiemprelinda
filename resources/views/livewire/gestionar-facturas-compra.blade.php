<div>
    <div class="p-6 text-gray-900 dark:text-gray-100">
        

        <div class="flex justify-between items-center mb-4">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Buscar por ID, proveedor o fecha..."
                class="block w-1/3 rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

            @can('create facturas-compra')
            <a href="{{ route('facturas-compra.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-md shadow-sm">
                Registrar Factura de Compra
            </a>
            @endcan
        </div>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Proveedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total USD</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registrado Por</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($facturas as $factura)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $factura->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $factura->proveedor->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $factura->fecha_factura->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">${{ number_format($factura->total_usd, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $factura->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="verFactura({{ $factura->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 font-semibold">Ver</button>
                                <a href="{{ route('facturas-compra.edit', $factura->id) }}" class="ml-4 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-200 font-semibold">Editar</a>
                                <button wire:click="confirmarEliminacion({{ $factura->id }})" class="ml-4 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 font-semibold">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                No se encontraron facturas de compra.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $facturas->links() }}
        </div>
    </div>

    {{-- Modal para Ver Factura --}}
    <x-modal wire:model="showVerFacturaModal">
        <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            @if ($facturaSeleccionada)
                <h2 class="text-2xl font-bold mb-4 border-b border-gray-300 dark:border-gray-600 pb-2">Detalle de Factura #{{ $facturaSeleccionada->id }}</h2>
                <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                    <div><strong>Proveedor:</strong> {{ $facturaSeleccionada->proveedor->nombre }}</div>
                    <div><strong>Fecha:</strong> {{ $facturaSeleccionada->fecha_factura->format('d/m/Y') }}</div>
                    <div><strong>Registrado por:</strong> {{ $facturaSeleccionada->user->name }}</div>
                    @if($facturaSeleccionada->tasaDeCambio)
                        <div><strong>Tasa aplicada:</strong> Bs. {{ number_format($facturaSeleccionada->tasaDeCambio->tasa, 4) }}</div>
                    @else
                        <div><strong>Tasa aplicada:</strong> No disponible</div>
                    @endif
                </div>

                <h3 class="text-lg font-semibold mt-6 mb-2">Productos</h3>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-md">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider">Producto</th>
                                <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider">Cantidad</th>
                                <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider">Precio Unit.</th>
                                <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facturaSeleccionada->detalles as $detalle)
                                <tr class="border-b dark:border-gray-600">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $detalle->producto->nombre }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right">{{ $detalle->cantidad }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right">${{ number_format($detalle->precio_compra_unitario, 2) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right">${{ number_format($detalle->subtotal_usd, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h3 class="text-lg font-semibold mt-6 mb-2">Pagos</h3>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-md">
                     <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider">Método de Pago</th>
                                <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($facturaSeleccionada->pagos as $pago)
                                <tr class="border-b dark:border-gray-600">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $pago->metodoPago->nombre }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right">${{ number_format($pago->monto_usd, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-2 text-center text-gray-500">No se registraron pagos específicos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="text-right mt-6 space-y-1">
                    <p class="text-lg font-bold">Total Factura: ${{ number_format($facturaSeleccionada->total_usd, 2) }}</p>
                    <p class="text-md text-gray-600 dark:text-gray-400">Total en Bs: {{ number_format($facturaSeleccionada->total_bs, 2) }}</p>
                </div>

            @else
                <p>Cargando datos de la factura...</p>
            @endif

            <div class="mt-6 flex justify-end">
                <x-secondary-button @click="$wire.set('showVerFacturaModal', false)">
                    Cerrar
                </x-secondary-button>
            </div>
        </div>
    </x-modal>

    {{-- Modal de Confirmación de Eliminación --}}
    <x-modal wire:model="showDeleteModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                ¿Estás seguro de que deseas eliminar esta factura?
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Esta acción no se puede deshacer. Se revertirá la entrada de stock asociada a esta factura. Por favor, confirma que deseas eliminar permanentemente esta factura.
            </p>
            <div class="mt-6 flex justify-end">
                <x-secondary-button @click="$wire.set('showDeleteModal', false)">
                    Cancelar
                </x-secondary-button>
                <x-danger-button class="ml-3" wire:click="deleteFactura">
                    Eliminar Factura
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>
