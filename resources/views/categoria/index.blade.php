<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Categorias
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('create categories')
                    <a href="{{ route('categorias.create') }}" class="text-blue-500 hover:text-blue-700">Crear Categoría</a>
                    @endcan
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($categorias as $categoria)
                                <tr>
                                    {{-- Imagen --}}
                                    <td>
                                        @if ($categoria->ruta_imagen)
                                            {{-- Usamos asset() y storage/ para acceder a la imagen pública --}}
                                            <img src="{{ asset('storage/' . $categoria->ruta_imagen) }}" alt="Imagen de {{ $categoria->nombre }}" class="h-24 w-24 object-cover rounded">
                                        @else
                                            Sin imagen
                                        @endif
                                    </td>

                                    {{-- Nombre --}}
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $categoria->nombre }}</td>
                                    
                                    {{-- Acciones --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                    
                                        {{-- Botón Editar --}}
                                        @can('edit categories')
                                        <a href="{{ route('categorias.edit', $categoria->id) }}" class="text-blue-500 hover:text-blue-700">Editar</a>
                                        @endcan
                                    
                                        {{-- Botón Eliminar --}}
                                        {{-- Usamos un formulario para el botón de eliminar --}}
                                        @can('delete categories')
                                        <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" style="display:inline;">
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
</x-app-layout>