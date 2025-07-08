<x-public-layout>
    <!-- Cabecera (Header) -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <!-- Icono Hamburguesa y Logo -->
            <div class="flex items-center">
                <button class="text-gray-500 focus:outline-none focus:text-gray-600 md:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-2xl font-bold text-gray-800 ml-4">SiempreLinda</h1>
            </div>
            <!-- Redes Sociales y Búsqueda -->
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Redes Sociales</span>
                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </header>

    <!-- Sección Principal de Contenido Visual/Informativo -->
    <section class="bg-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between">
            <div class="w-full md:w-3/4 h-64 bg-gray-300 flex items-center justify-center text-gray-600 text-xl">
                Imagen principal / Mensaje
            </div>
            <div class="w-full md:w-1/4 mt-4 md:mt-0 md:ml-4 space-y-4">
                <div class="h-32 bg-gray-300 flex items-center justify-center text-gray-600">Nuestros clientes</div>
                <div class="h-32 bg-gray-300 flex items-center justify-center text-gray-600">Reseñas</div>
            </div>
        </div>
    </section>

    <!-- Sección de Marcas -->
    <section class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-800 text-2xl font-bold">
            Marcas
        </div>
    </section>

    <!-- Sección de Productos Destacados -->
    <section class="bg-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row space-y-8 md:space-y-0 md:space-x-8">
            <div class="w-full md:w-1/2 bg-white p-6 shadow rounded-lg">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Lo Nuevo</h3>
                <div class="h-48 bg-gray-300 flex items-center justify-center text-gray-600">Productos Nuevos</div>
            </div>
            <div class="w-full md:w-1/2 bg-white p-6 shadow rounded-lg">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Lo más Vendido</h3>
                <div class="h-48 bg-gray-300 flex items-center justify-center text-gray-600">Productos más Vendidos</div>
            </div>
        </div>
    </section>

    <!-- Secciones de Ofertas y Recomendaciones -->
    <section class="bg-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row space-y-8 md:space-y-0 md:space-x-8">
            <div class="w-full md:w-1/2 bg-white p-6 shadow rounded-lg">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Recomendado</h3>
                <div class="h-48 bg-gray-300 flex items-center justify-center text-gray-600">Productos Recomendados</div>
            </div>
            <div class="w-full md:w-1/2 bg-white p-6 shadow rounded-lg">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Oferta de la Semana</h3>
                <div class="h-48 bg-gray-300 flex items-center justify-center text-gray-600">Oferta</div>
            </div>
        </div>
    </section>

    <!-- Sección de Categorías -->
    <section class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-800 text-2xl font-bold">
            Categorías
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