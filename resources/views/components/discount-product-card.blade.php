<div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300 ease-in-out">
    @if ($product->ruta_imagen)
        <img src="{{ asset('storage/' . $product->ruta_imagen) }}" alt="{{ $product->nombre }}" class="w-full h-48 object-cover">
    @else
        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">Sin imagen</div>
    @endif
    <div class="p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $product->nombre }}</h3>
        <p class="text-gray-500 line-through text-sm">${{ number_format($product->precio, 2) }}</p>
        <p class="text-red-600 text-xl font-bold">${{ number_format($product->precio_descuento, 2) }}</p>
    </div>
</div>