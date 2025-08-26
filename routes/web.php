<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Models\Producto;

Route::get('/', function () {
    $marcas = App\Models\Marca::all();
    $productos = Producto::where('nuevo', true)->latest()->take(8)->get(); // Obtener los últimos 8 productos nuevos
    $productosRecomendados = Producto::where('recomendado', true)->latest()->take(8)->get(); // Obtener los últimos 8 productos recomendados
    $productosOferta = Producto::where('descuento', true)->latest()->take(8)->get(); // Obtener los últimos 8 productos en oferta
    $categorias = App\Models\Categoria::all(); // Obtener todas las categorías
    return view('welcome', compact('marcas', 'productos', 'productosRecomendados', 'categorias', 'productosOferta'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categorias', CategoriaController::class);
    Route::resource('marcas', MarcaController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('proveedores', App\Http\Controllers\ProveedorController::class)->parameters(['proveedores' => 'proveedor']);
    Route::get('/stock-movements', App\Livewire\GestionarMovimientosStock::class)->name('stock-movements.index');

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Route for POS Client Test
    Route::get('/pos/client-test', function () {
        return view('pos.client-test');
    })->name('pos.client-test');
});

require __DIR__.'/auth.php';
