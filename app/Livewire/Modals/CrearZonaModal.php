<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use App\Models\Ubicacion;
use App\Models\Zona;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

class CrearZonaModal extends Component
{
    public $show;

    #[Rule('required|string|max:255')]
    public $nombre;

    #[Rule('nullable|string|max:255')]
    public $descripcion;

    #[Rule('required|exists:ubicaciones,id')]
    public $ubicacion_id;

    public function mount($show, $ubicacionId = null)
    {
        $this->show = $show;
        if ($ubicacionId) {
            $this->ubicacion_id = $ubicacionId;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $zona = Zona::create([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'ubicacion_id' => $this->ubicacion_id,
            ]);

            $this->dispatch('zonaCreated', zonaId: $zona->id, zonaNombre: $zona->nombre, ubicacionId: $zona->ubicacion_id);
            $this->dispatch('app-notification-success', message: 'Zona creada exitosamente.');
            $this->dispatch('close-crear-zona-modal');
        } catch (\Exception $e) {
            $this->dispatch('app-notification-error', message: 'Error al crear la zona: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $ubicaciones = Ubicacion::orderBy('nombre')->get();
        return view('livewire.modals.crear-zona-modal', [
            'ubicaciones' => $ubicaciones,
        ]);
    }
}