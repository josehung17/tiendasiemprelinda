<x-app-layout>
    <x-slot name='header'>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Productos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('create products')
                    <a href="{{ route('productos.create') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">Crear Producto</a>
                    @endcan
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Imagen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio con descuento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nuevo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Recomendado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descuento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($productos as $producto)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($producto->ruta_imagen)
                                                {{-- Usamos asset() y storage/ para acceder a la imagen pública --}}
                                                <img src="{{ asset('storage/' . $producto->ruta_imagen) }}" alt="Imagen de {{ $producto->nombre }}" class="h-12 w-12 object-cover rounded">
                                            @else
                                                <span class="text-xs text-gray-400">Sin imagen</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $producto->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $producto->precio }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $producto->precio_descuento }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $producto->nuevo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $producto->nuevo ? 'Sí' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $producto->recomendado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $producto->recomendado ? 'Sí' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $producto->descuento ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $producto->descuento ? 'Sí' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @can('view products')
                                            <a href="{{ route('productos.show', $producto->id) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                            @endcan
                                            @can('edit products')
                                            <a href="{{ route('productos.edit', $producto->id) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                            @endcan
                                            @can('delete products')
                                            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
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
    </div> 
</x-app-layout>
