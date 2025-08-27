<div>
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="flex justify-between items-center mb-4">
            {{-- Input de búsqueda --}}
            <input
                wire:model.live="search"
                type="text"
                placeholder="Buscar facturas de compra..."
                class="block w-1/3 rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

            {{-- Botón de crear --}}
            @can('create facturas-compra')
            <a href="{{ route('facturas-compra.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-md shadow-sm">
                Registrar Factura de Compra
            </a>
            @endcan
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID Factura</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Proveedor</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha Factura</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tasa Aplicada</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total USD</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Bs</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registrado Por</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($facturas as $factura)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $factura->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $factura->proveedor->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $factura->fecha_factura->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ number_format($factura->tasa_cambio_aplicada, 4) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">${{ number_format($factura->total_usd, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">Bs. {{ number_format($factura->total_bs, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $factura->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{-- @can('edit facturas-compra')
                                <a href="{{ route('facturas-compra.edit', $factura->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">Editar</a>
                                @endcan
                                @can('delete facturas-compra')
                                <button wire:click="deleteFacturaCompra({{ $factura->id }})" class="ml-4 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">Eliminar</button>
                                @endcan --}}
                                <span class="text-gray-500 dark:text-gray-400">Acciones (Pendiente)</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                No se encontraron facturas de compra.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $facturas->links() }}
        </div>
    </div>
</div>