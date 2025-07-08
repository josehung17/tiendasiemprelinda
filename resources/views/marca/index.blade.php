<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Marcas
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('create marcas')
                    <a href="{{ route('marcas.create') }}" class="text-blue-500 hover:text-blue-700">Crear Marca</a>
                    @endcan
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($marcas as $marca)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $marca->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $marca->nombre }}</td>
                                    <td>
                                        @if ($marca->ruta_imagen)
                                            {{-- Usamos asset() y storage/ para acceder a la imagen pública --}}
                                            <img src="{{ asset('storage/' . $marca->ruta_imagen) }}" alt="Imagen de {{ $marca->nombre }}" style="max-width: 100px; height: auto;">
                                        @else
                                            Sin imagen
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @can('edit marcas')
                                        <a href="{{ route('marcas.edit', $marca->id) }}" class="text-blue-500 hover:text-blue-700">Editar</a>
                                        @endcan
                                        @can('delete marcas')
                                        <form action="{{ route('marcas.destroy', $marca->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Eliminar</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                           @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
</x-app-layout>