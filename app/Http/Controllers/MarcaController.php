<?php
namespace App\Http\Controllers;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;


class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $marcas = Marca::all();
        return view('marca.index', compact('marcas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('marca.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
   
        $imagePath = null;

        // Validación directa en el controlador
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'ruta_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('ruta_imagen')) {
            /** @var \Illuminate\Http\UploadedFile $uploadedFile */
            $uploadedFile = $request->file('ruta_imagen');
            $imageName = time() . '.' . $uploadedFile->extension();
            $imagePath = $uploadedFile->storeAs('marcas', $imageName, 'public');
        }
    
        Marca::create([
            'nombre' => $request->nombre,
        'ruta_imagen' => $imagePath, // Guarda la ruta de la imagen en la columna 'ruta_imagen'
    ]);
    return redirect()->route('marcas.index')->with('success', 'Marca creada exitosamente con imagen.');
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
    public function edit(Marca $marca)
    {
        return view('marca.edit', compact('marca'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marca $marca)
    {
        $imagePath = $marca->ruta_imagen;

        // Validación directa en el controlador
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'ruta_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('ruta_imagen')) {
            // Eliminar la imagen antigua si existe en el almacenamiento 🗑️
            if ($marca->ruta_imagen && Storage::disk('public')->exists($marca->ruta_imagen)) {
                Storage::disk('public')->delete($marca->ruta_imagen);
            }

            /** @var \Illuminate\Http\UploadedFile $uploadedFile */ // <-- ¡AÑADE ESTA LÍNEA!
            $uploadedFile = $request->file('ruta_imagen'); // <-- ¡USA ESTE MÉTODO!

            $imageName = time() . '.' . $uploadedFile->extension();
            $imagePath = $uploadedFile->storeAs('marcas', $imageName, 'public');
        }

        $marca->update([
            'nombre' => $request->nombre,
            'ruta_imagen' => $imagePath,
        ]);

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marca $marca)
    {
        // Eliminar la imagen asociada si existe
        if ($marca->ruta_imagen && Storage::disk('public')->exists($marca->ruta_imagen)) {
            Storage::disk('public')->delete($marca->ruta_imagen);
        }

        // Eliminar la marca de la base de datos
        $marca->delete();

        return redirect()->route('marcas.index')->with('success', 'Marca eliminada exitosamente.');
    }
}