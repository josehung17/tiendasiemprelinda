<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TasaDeCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ActualizarTasaBoton extends Component
{
    public $message = '';

    public function actualizarTasa()
    {
        $today = Carbon::today();

        // Verificar si ya se actualizó la tasa hoy
        $tasaExistente = TasaDeCambio::where('moneda', 'USD') // Asumimos USD como la moneda principal
                                    ->whereDate('fecha_actualizacion', $today)
                                    ->first();

        if ($tasaExistente) {
            $this->message = 'La tasa de cambio ya fue actualizada hoy.';
            return;
        }

        // Lógica para obtener la tasa de la API externa
        try {
            $response = Http::get('https://bcv-api.rafnixg.dev/rates/');
            if ($response->successful()) {
                $data = $response->json();
                // Asumiendo que la API devuelve la tasa del dólar bajo la clave 'dollar'
                if (isset($data['dollar'])) {
                    $nuevaTasa = (float) $data['dollar'];
                } else {
                    $this->message = 'Error: Formato de datos inesperado de la API (falta la clave "dollar").';
                    \Log::error('BCV API: Unexpected data format (missing "dollar" key)', ['response_data' => $data]);
                    return;
                }
            } else {
                $this->message = 'Error al obtener la tasa de la API: ' . $response->status() . '.';
                \Log::error('BCV API: Unsuccessful response', ['status' => $response->status(), 'body' => $response->body()]);
                return;
            }
        } catch (\Exception $e) {
            $this->message = 'Error de conexión al obtener la tasa de la API: ' . $e->getMessage();
            \Log::error('BCV API: Connection error', ['exception' => $e->getMessage()]);
            return;
        }

        if ($nuevaTasa === null) {
            $this->message = 'No se pudo obtener la tasa de cambio de la API.';
            return;
        }

        // Guardar o actualizar la tasa en la base de datos
        TasaDeCambio::updateOrCreate(
            ['moneda' => 'USD'], // Busca por moneda
            [
                'tasa' => $nuevaTasa,
                'fecha_actualizacion' => $today,
            ]
        );

        $this->message = 'Tasa de cambio actualizada correctamente a: ' . $nuevaTasa;
    }

    public function render()
    {
        return view('livewire.actualizar-tasa-boton');
    }
}