<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = Categoria::all();
        return view('categoria.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categoria.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $imagePath = null;

        // Validación directa en el controlador
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
            'ruta_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('ruta_imagen')) {
            /** @var \Illuminate\Http\UploadedFile $uploadedFile */
            $uploadedFile = $request->file('ruta_imagen');
            $imageName = time() . '.' . $uploadedFile->extension();
            $imagePath = $uploadedFile->storeAs('categorias', $imageName, 'public');
        }
    
        Categoria::create([
            'nombre' => $request->nombre,
            'ruta_imagen' => $imagePath, // Guarda la ruta de la imagen en la columna 'ruta_imagen'
        ]);
        return redirect()->route('categorias.index')->with('success', 'Categoria creada exitosamente con imagen.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('categoria.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);
        $imagePath = $categoria->ruta_imagen;

        $request->validate([
            'nombre' => 'required|string|max:255',
            'ruta_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('ruta_imagen')) {

            // Eliminar la imagen antigua si existe en el almacenamiento 🗑️
            if ($categoria->ruta_imagen && Storage::disk('public')->exists($categoria->ruta_imagen)) {
                Storage::disk('public')->delete($categoria->ruta_imagen);
            }

            /** @var \Illuminate\Http\UploadedFile $uploadedFile */
            $uploadedFile = $request->file('ruta_imagen');
            $imageName = time() . '.' . $uploadedFile->extension();
            $imagePath = $uploadedFile->storeAs('categorias', $imageName, 'public');
        }

        $categoria->update([
            'nombre' => $request->nombre,
            'ruta_imagen' => $imagePath,
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoria actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        // Eliminar la imagen asociada si existe
        if ($categoria->ruta_imagen && Storage::disk('public')->exists($categoria->ruta_imagen)) {
            Storage::disk('public')->delete($categoria->ruta_imagen);
        }

        // Eliminar la categoria de la base de datos
        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoria eliminada exitosamente.');
    }
}
