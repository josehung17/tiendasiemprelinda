<?php

namespace App\Livewire;

use App\Models\Ubicacion;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GestionarConfiguracionTienda extends Component
{
    public $ubicacionesDisponibles = [];
    public $selectedUbicacionId;
    public $productos = [];
    public $ubicacionActivaNombre;

    public function mount()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $this->ubicacionesDisponibles = Ubicacion::where('tipo', 'tienda')->orderBy('nombre')->get();
        } else {
            $ubicacionIdSesion = session('pos_location_id');
            if ($ubicacionIdSesion) {
                $this->selectedUbicacionId = $ubicacionIdSesion;
                $this->cargarProductosParaUbicacion();
            }
        }
    }

    public function updatedSelectedUbicacionId($value)
    {
        $this->selectedUbicacionId = $value;
        $this->cargarProductosParaUbicacion();
    }

    public function cargarProductosParaUbicacion()
    {
        if (!$this->selectedUbicacionId) {
            $this->productos = [];
            $this->ubicacionActivaNombre = null;
            return;
        }

        $ubicacion = Ubicacion::find($this->selectedUbicacionId);
        if (!$ubicacion) {
            $this->productos = [];
            $this->ubicacionActivaNombre = null;
            return;
        }
        
        // Seguridad: Un no-admin solo puede cargar su propia tienda de la sesión
        if (!Auth::user()->hasRole('admin') && $this->selectedUbicacionId != session('pos_location_id')) {
             $this->dispatch('app-notification-error', ['message' => 'Acceso no autorizado.']);
             $this->productos = [];
             $this->ubicacionActivaNombre = null;
             return;
        }

        $this->ubicacionActivaNombre = $ubicacion->nombre;

        $this->productos = Producto::whereHas('ubicaciones', function ($query) {
            $query->where('ubicacion_id', $this->selectedUbicacionId);
        })
        ->with(['zonasStock' => function ($query) {
            $query->where('ubicacion_id', $this->selectedUbicacionId);
        }])
        ->orderBy('nombre')
        ->get();
    }

    public function setPredeterminada($productoId, $zonaId)
    {
        // Seguridad: Verificar que el usuario tiene permiso sobre la ubicación activa
        if (Auth::user()->hasRole('admin') || $this->selectedUbicacionId == session('pos_location_id')) {
            
            try {
                DB::transaction(function () use ($productoId, $zonaId) {
                    // 1. Quitar cualquier otra zona predeterminada para este producto en CUALQUIER ubicación
                    DB::table('producto_ubicacion')
                        ->where('producto_id', $productoId)
                        ->update(['es_zona_predeterminada_pos' => false]);

                    // 2. Establecer la nueva zona predeterminada
                    DB::table('producto_ubicacion')
                        ->where('producto_id', $productoId)
                        ->where('zona_id', $zonaId)
                        ->where('ubicacion_id', $this->selectedUubicacionId)
                        ->update(['es_zona_predeterminada_pos' => true]);
                });

                // Recargar los productos para reflejar el cambio en la UI
                $this->cargarProductosParaUbicacion();

                $this->dispatch('app-notification-success', ['message' => 'Zona predeterminada para el POS actualizada correctamente.']);

            } catch (\Exception $e) {
                $this->dispatch('app-notification-error', ['message' => 'Error al actualizar la zona predeterminada: ' . $e->getMessage()]);
            }

        } else {
            $this->dispatch('app-notification-error', ['message' => 'Acceso no autorizado para realizar esta acción.']);
        }
    }

    public function render()
    {
        return view('livewire.gestionar-configuracion-tienda');
    }
}