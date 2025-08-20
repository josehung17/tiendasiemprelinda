<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::all();
        return view('cliente.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposDocumento = ['Venezolano', 'Extranjero', 'Jurídico'];
        return view('cliente.create', compact('tiposDocumento'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tipo_documento' => 'required|in:Venezolano,Extranjero,Jurídico',
            'nombre' => 'required|string|max:255',
            'numero_documento' => 'required|string|max:255|unique:clientes,numero_documento',
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'direccion' => 'nullable|string',
        ]);

        Cliente::create($validatedData);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        return view('cliente.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        $tiposDocumento = ['Venezolano', 'Extranjero', 'Jurídico'];
        return view('cliente.edit', compact('cliente', 'tiposDocumento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validatedData = $request->validate([
            'tipo_documento' => 'required|in:Venezolano,Extranjero,Jurídico',
            'nombre' => 'required|string|max:255',
            'numero_documento' => 'required|string|max:255|unique:clientes,numero_documento,' . $cliente->id,
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
            'direccion' => 'nullable|string',
        ]);

        $cliente->update($validatedData);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado exitosamente.');
    }
}