<div>
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="flex justify-between items-center mb-4">
            {{-- Input de búsqueda --}}
            <input 
                wire:model.live="search" 
                type="text" 
                placeholder="Buscar marcas..." 
                class="block w-1/3 rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

            {{-- Botón de crear --}}
            @can('create marcas')
            <a href="{{ route('marcas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-md shadow-sm">
                Crear Marca
            </a>
            @endcan
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Imagen</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($marcas as $marca)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($marca->ruta_imagen)
                                    <img src="{{ asset('storage/' . $marca->ruta_imagen) }}" alt="Imagen de {{ $marca->nombre }}" class="h-16 w-16 object-cover rounded">
                                @else
                                    <span class="text-gray-400">Sin imagen</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $marca->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @can('edit marcas')
                                <a href="{{ route('marcas.edit', $marca->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">Editar</a>
                                @endcan
                                
                                @can('delete marcas')
                                <form action="{{ route('marcas.destroy', $marca->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta marca?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-4 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">Eliminar</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                No se encontraron marcas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $marcas->links() }}
        </div>
    </div>
</div>