<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TasaDeCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ActualizarTasaBoton extends Component
{

    public function actualizarTasa()
    {
        $today = Carbon::today();

        // Verificar si ya se actualizó la tasa hoy
        $tasaExistente = TasaDeCambio::whereDate('fecha_actualizacion', $today)->first();

        if ($tasaExistente) {
            $this->dispatch('app-notification-error', message: 'La tasa de cambio ya fue actualizada hoy.');
            return;
        }

        // Lógica para obtener la tasa de la API externa
        $nuevaTasa = null;
        try {
            $response = Http::get('https://bcv-api.rafnixg.dev/rates/');
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['dollar'])) {
                    $nuevaTasa = (float) $data['dollar'];
                } else {
                    $this->dispatch('app-notification-error', message: 'Error: Formato de datos inesperado de la API.');
                    \Log::error('BCV API: Unexpected data format (missing "dollar" key)', ['response_data' => $data]);
                    return;
                }
            } else {
                $this->dispatch('app-notification-error', message: 'Error al obtener la tasa de la API: ' . $response->status() . '.');
                \Log::error('BCV API: Unsuccessful response', ['status' => $response->status(), 'body' => $response->body()]);
                return;
            }
        } catch (\Exception $e) {
            $this->dispatch('app-notification-error', message: 'Error de conexión al obtener la tasa de la API.');
            \Log::error('BCV API: Connection error', ['exception' => $e->getMessage()]);
            return;
        }

        if ($nuevaTasa === null) {
            $this->dispatch('app-notification-error', message: 'No se pudo obtener la tasa de cambio de la API.');
            return;
        }

        // Crear un nuevo registro para la tasa de cambio
        TasaDeCambio::create([
            'moneda' => 'USD',
            'tasa' => $nuevaTasa,
            'fecha_actualizacion' => $today,
        ]);

        $this->dispatch('app-notification-success', message: 'Nueva tasa de cambio guardada: ' . $nuevaTasa);
    }

    public function render()
    {
        return view('livewire.actualizar-tasa-boton');
    }
}