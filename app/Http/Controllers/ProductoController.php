<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Marca;      
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::all();
        return view('producto.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::all();
        $marcas = Marca::all();
        return view('producto.create', compact('categorias', 'marcas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Valida los datos del formulario
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:productos',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'precio_descuento' => 'nullable|numeric|min:0', // Añadido para el nuevo campo
            'ruta_imagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'marca_id' => 'required|exists:marcas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'stock' => 'required|integer|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'margen_ganancia' => 'required|numeric|min:0|max:100',
        ]);

        // 2. Maneja la subida del archivo de imagen
        if ($request->hasFile('ruta_imagen')) {
            $path = $request->file('ruta_imagen')->store('productos', 'public');
            $validatedData['ruta_imagen'] = $path;
        }

        // 3. Prepara los datos de los checkboxes
        $validatedData['nuevo'] = $request->has('nuevo');
        $validatedData['recomendado'] = $request->has('recomendado');
        $validatedData['descuento'] = $request->has('descuento');

        // 4. Crea el producto en la base de datos
        Producto::create($validatedData);

        // 5. Redirige al usuario a la lista de productos con un mensaje de éxito.
        return redirect()->route('productos.index')->with('success', '¡Producto creado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = Producto::findOrFail($id);
        return view('producto.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        $marcas = Marca::all();
        return view('producto.edit', compact('producto', 'categorias', 'marcas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $producto = Producto::findOrFail($id);

        // 1. Valida los datos del formulario
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:productos,nombre,' . $id,
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'precio_descuento' => 'nullable|numeric|min:0', // Añadido para el nuevo campo
            'ruta_imagen' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'marca_id' => 'required|exists:marcas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'stock' => 'required|integer|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'margen_ganancia' => 'required|numeric|min:0|max:100',
        ]);

        // 2. Maneja la subida de una nueva imagen
        if ($request->hasFile('ruta_imagen')) {
            // Opcional: Eliminar la imagen anterior si existe
            if ($producto->ruta_imagen) {
                Storage::disk('public')->delete($producto->ruta_imagen);
            }
            $path = $request->file('ruta_imagen')->store('productos', 'public');
            $validatedData['ruta_imagen'] = $path;
        }

        // 3. Prepara los datos de los checkboxes
        $validatedData['nuevo'] = $request->has('nuevo');
        $validatedData['recomendado'] = $request->has('recomendado');
        $validatedData['descuento'] = $request->has('descuento');

        // 4. Actualiza el producto
        $producto->update($validatedData);

        // 5. Redirige con un mensaje de éxito
        return redirect()->route('productos.index')->with('success', '¡Producto actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);

        // Eliminar la imagen asociada del almacenamiento
        if ($producto->ruta_imagen) {
            Storage::disk('public')->delete($producto->ruta_imagen);
        }

        // Eliminar el producto de la base de datos
        $producto->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->route('productos.index')->with('success', '¡Producto eliminado exitosamente!');
    }
}
