<?php

namespace App\Livewire;

use App\Models\Ubicacion;
use App\Models\Zona;
use Livewire\Component;
use Illuminate\Support\Facades\Log; // Add this line

class GestionarUbicaciones extends Component
{
    // Propiedades del componente
    public $ubicaciones;

    // Propiedades para el modal de Ubicacion
    public $showUbicacionModal = false;
    public $ubicacionId;
    public $nombre;
    public $tipo = 'almacen'; // Valor por defecto
    public $direccion;

    // Propiedades para el modal de Zona
    public $showZonaModal = false;
    public ?Ubicacion $selectedUbicacion = null;
    public $zonaId;
    public $zonaNombre;

    // Propiedades para el modal de confirmación de borrado
    public $showDeleteModal = false;
    public $itemToDelete;
    public $itemType;


    public function render()
    {
        $this->ubicaciones = Ubicacion::with('zonas')->orderBy('nombre')->get();
        return view('livewire.gestionar-ubicaciones');
    }

    // --- Métodos para Ubicaciones ---

    public function createUbicacion()
    {
        $this->resetUbicacionInputFields();
        $this->showUbicacionModal = true;
    }

    public function editUbicacion(Ubicacion $ubicacion)
    {
        $this->ubicacionId = $ubicacion->id;
        $this->nombre = $ubicacion->nombre;
        $this->tipo = $ubicacion->tipo;
        $this->direccion = $ubicacion->direccion;
        $this->showUbicacionModal = true;
    }

    public function saveUbicacion()
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:almacen,tienda',
            'direccion' => 'nullable|string|max:255',
        ];

        // Añadir regla de unicidad solo si es una nueva ubicación o el nombre ha cambiado
        if (!$this->ubicacionId) {
            $rules['nombre'] .= '|unique:ubicaciones,nombre';
        } else {
            $rules['nombre'] .= '|unique:ubicaciones,nombre,' . $this->ubicacionId;
        }

        $this->validate($rules);

        $data = [
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'direccion' => $this->direccion,
        ];

        Ubicacion::updateOrCreate(
            ['id' => $this->ubicacionId],
            $data
        );

        $this->closeModals();

        $this->dispatch('app-notification-success', message: 'Ubicación guardada correctamente.');
    }

    private function resetUbicacionInputFields()
    {
        $this->ubicacionId = null;
        $this->nombre = '';
        $this->tipo = 'almacen';
        $this->direccion = '';
    }

    // --- Métodos para Zonas ---

    public function createZona(Ubicacion $ubicacion)
    {
        $this->selectedUbicacion = $ubicacion;
        $this->resetZonaInputFields();
        $this->showZonaModal = true;
    }

    public function editZona(Zona $zona)
    {
        $this->selectedUbicacion = $zona->ubicacion;
        $this->zonaId = $zona->id;
        $this->zonaNombre = $zona->nombre;
        $this->showZonaModal = true;
    }

    public function saveZona()
    {
        $this->validate([
            'zonaNombre' => 'required|string|max:255|unique:zonas,nombre,' . $this->zonaId . ',id,ubicacion_id,' . $this->selectedUbicacion->id
        ], [
            'zonaNombre.unique' => 'El nombre de la zona ya existe en esta ubicación.'
        ]);

        Zona::updateOrCreate(
            ['id' => $this->zonaId],
            [
                'nombre' => $this->zonaNombre,
                'ubicacion_id' => $this->selectedUbicacion->id,
            ]
        );

        $this->closeModals();

        $this->dispatch('app-notification-success', message: 'Zona guardada correctamente.');
    }

    private function resetZonaInputFields()
    {
        $this->zonaId = null;
        $this->zonaNombre = '';
    }

    // --- Métodos para Borrado ---

    public function confirmDelete($type, $id)
    {
        $this->itemType = $type;
        $this->itemToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteItem()
    {
        if ($this->itemType === 'ubicacion') {
            Ubicacion::find($this->itemToDelete)->delete();
            $message = 'Ubicación eliminada correctamente.';
        } elseif ($this->itemType === 'zona') {
            Zona::find($this->itemToDelete)->delete();
            $message = 'Zona eliminada correctamente.';
        }

        $this->closeModals();

        $this->dispatch('app-notification-success', message: $message ?? 'Elemento eliminado.');
    }

    public function closeModals()
    {
        $this->showUbicacionModal = false;
        $this->showZonaModal = false;
        $this->showDeleteModal = false;
    }

    
}