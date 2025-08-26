<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use App\Models\Cliente;

class CreateClientModal extends Component
{
    public $nombre;
    public $tipo_documento = 'Venezolano'; // Default value
    public $numero_documento;
    public $telefono;
    public $email; // Added email as it's in the migration
    public $direccion;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'tipo_documento' => 'required|string|in:Venezolano,Extranjero,Jurídico',
        'numero_documento' => 'required|string|max:20|unique:clientes,numero_documento',
        'telefono' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255|unique:clientes,email',
        'direccion' => 'nullable|string|max:255',
    ];

    public function saveClient()
    {
        $this->validate();

        $client = Cliente::create([
            'nombre' => $this->nombre,
            'tipo_documento' => $this->tipo_documento,
            'numero_documento' => $this->numero_documento,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
        ]);

        $this->dispatch('clientCreated', $client->id);
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->tipo_documento = 'Venezolano';
        $this->numero_documento = '';
        $this->telefono = '';
        $this->email = '';
        $this->direccion = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.modals.create-client-modal');
    }
}
