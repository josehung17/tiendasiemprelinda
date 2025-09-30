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
        $nuevaTasa = null;
        $fechaVigencia = null;

        // Lógica para obtener la tasa de la API externa
        try {
            $response = Http::get('https://bcv-api.rafnixg.dev/rates/');
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['dollar'], $data['date'])) {
                    $nuevaTasa = (float) $data['dollar'];
                    $fechaVigencia = Carbon::parse($data['date'])->toDateString();

                    // Verificar si ya existe una tasa para la fecha de vigencia informada por la API
                    $tasaExistente = TasaDeCambio::whereDate('fecha_vigencia', $fechaVigencia)->first();

                    if ($tasaExistente) {
                        $this->dispatch('app-notification-error', message: 'La tasa para la fecha de vigencia ' . $fechaVigencia . ' ya existe.');
                        return;
                    }
                } else {
                    $this->dispatch('app-notification-error', message: 'Error: Formato de datos inesperado de la API (faltan claves "dollar" o "date").');
                    \Log::error('BCV API: Unexpected data format', ['response_data' => $data]);
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
            'fecha_vigencia' => $fechaVigencia,
        ]);

        $this->dispatch('app-notification-success', message: 'Nueva tasa de cambio guardada (Vigencia: ' . $fechaVigencia . '): ' . $nuevaTasa);
    }

    public function render()
    {
        return view('livewire.actualizar-tasa-boton');
    }
}