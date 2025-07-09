<x-public-layout>
    <!-- Cabecera (Header) -->
    <header class="bg-white shadow">
        @include('layouts.navigation-web')
    </header>

    <!-- Sección Principal de Contenido Visual/Informativo -->
    <section class="bg-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between">
            <div class="w-full md:w-3/4 h-64 bg-gray-300 flex items-center justify-center text-gray-600 text-xl">
                Imagen principal / Mensaje
            </div>
            <div class="w-full md:w-1/4 mt-4 md:mt-0 md:ml-4 space-y-4">
                <div class="bg-white p-6 shadow rounded-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Nuestros Clientes</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Ejemplo de imágenes de clientes. Deberías reemplazarlas con tus propias imágenes. -->
                        <img src="{{ asset('assets/images/client_placeholder 1.jpg') }}" alt="Cliente 1" class="w-full h-24 object-cover rounded-lg shadow-md">
                        <img src="{{ asset('assets/images/client_placeholder 2.jpg') }}" alt="Cliente 2" class="w-full h-24 object-cover rounded-lg shadow-md">
                        <img src="{{ asset('assets/images/client_placeholder 3.jpg') }}" alt="Cliente 3" class="w-full h-24 object-cover rounded-lg shadow-md">
                        <img src="{{ asset('assets/images/client_placeholder 4.jpg') }}" alt="Cliente 4" class="w-full h-24 object-cover rounded-lg shadow-md">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Marcas -->
    <section id="marcas" class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-gray-800 text-2xl font-bold mb-4">Marcas</h2>
            <div class="overflow-x-auto whitespace-nowrap py-4">
                <div class="inline-flex space-x-8 animate-marquee">
                    @foreach($marcas as $marca)
                        <div class="flex-shrink-0 w-24 h-24 rounded-full overflow-hidden shadow-lg flex items-center justify-center transform hover:scale-105 transition-transform duration-300 ease-in-out animate-float">
                            @if ($marca->ruta_imagen)
                                <img src="{{ asset('storage/' . $marca->ruta_imagen) }}" alt="Imagen de {{ $marca->nombre }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-gray-600 text-center text-sm">{{ $marca->nombre }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Productos Destacados -->
    <section class="bg-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-gray-800 text-2xl font-bold mb-8">Lo Nuevo</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach($productos as $producto)
                    <x-product-card :product="$producto" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Sección de Ofertas de la Semana -->
    <section class="bg-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-gray-800 text-2xl font-bold mb-8">Ofertas de la Semana</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach($productosOferta as $producto)
                    <x-discount-product-card :product="$producto" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Sección de Productos Recomendados -->
    <section class="bg-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-gray-800 text-2xl font-bold mb-8">Recomendado</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach($productosRecomendados as $producto)
                    <x-product-card :product="$producto" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Sección de Categorías -->
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-gray-800 text-2xl font-bold mb-8">Categorías</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @foreach($categorias as $categoria)
                    <a href="#" class="block bg-gray-100 rounded-lg shadow-md p-4 text-center transform hover:scale-105 transition-transform duration-300 ease-in-out group">
                        <div class="w-24 h-24 mx-auto mb-4 bg-gray-300 rounded-full flex items-center justify-center group-hover:bg-pink-200 transition-colors duration-300 overflow-hidden">
                            @if ($categoria->ruta_imagen)
                                <img src="{{ asset('storage/' . $categoria->ruta_imagen) }}" alt="{{ $categoria->nombre }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-12 h-12 text-gray-600 group-hover:text-pink-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            @endif
                        </div>
                        <p class="text-lg font-semibold text-gray-800 group-hover:text-pink-700 transition-colors duration-300">{{ $categoria->nombre }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Pie de Página (Footer) -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <h4 class="text-xl font-bold mb-2">FAQ / Enlaces Rápidos</h4>
                <p>Contenido de FAQ o enlaces</p>
            </div>
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-8">
                <div>
                    <h4 class="text-xl font-bold mb-2">Redes Sociales</h4>
                    <p>Iconos de redes sociales</p>
                </div>
                <div>
                    <h4 class="text-xl font-bold mb-2">Ubicación</h4>
                    <p>Dirección de la tienda</p>
                </div>
            </div>
        </div>
    </footer>
</x-public-layout>