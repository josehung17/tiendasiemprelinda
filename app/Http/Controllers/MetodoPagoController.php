<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('metodo_pago.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('metodo_pago.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'divisa' => 'required|string|max:10',
        ]);

        MetodoPago::create($request->all());

        return redirect()->route('metodos-pago.index')->with('success', 'Método de pago creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not used for this CRUD
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MetodoPago $metodoPago)
    {
        return view('metodo_pago.edit', compact('metodoPago'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetodoPago $metodoPago)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'divisa' => 'required|string|max:10',
        ]);

        $metodoPago->update($request->all());

        return redirect()->route('metodos-pago.index')->with('success', 'Método de pago actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetodoPago $metodoPago)
    {
        $metodoPago->delete();

        return redirect()->route('metodos-pago.index')->with('success', 'Método de pago eliminado exitosamente.');
    }
}
