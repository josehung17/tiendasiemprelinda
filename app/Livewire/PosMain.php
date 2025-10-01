<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\FacturaVenta;
use App\Models\FacturaVentaDetalle;
use App\Models\TasaDeCambio;
use App\Models\Ubicacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class PosMain extends Component
{
    // POS State
    public $selectedClient = null;

    // Location and Modal State
    public $locationIsSelected = false;
    public $availableLocations = [];
    public $selectedLocationToSet;
    public $activeLocationId;
    public $activeLocationName;

    // Modal Steps
    public $modalStep = 'location';
    public $rateInfo = [];
    public ?TasaDeCambio $originalValidRate = null;
    public $manualRate;
    public $manualRateDate;

    // Active Exchange Rate
    public ?TasaDeCambio $activeExchangeRate = null;

    public function mount()
    {
        $this->availableLocations = Ubicacion::where('tipo', 'tienda')->orderBy('nombre')->get();
        $this->manualRateDate = Carbon::today()->toDateString();

        if (session()->has('pos_location_id')) {
            $location = Ubicacion::find(session('pos_location_id'));
            if ($location) {
                $this->selectedLocationToSet = $location->id;
                $this->setLocation();
            } else {
                session()->forget('pos_location_id');
                $this->resetModalState();
            }
        }
    }

    // Step 1: User clicks "Confirmar Tienda"
    public function setLocation()
    {
        $this->validate(['selectedLocationToSet' => 'required|exists:ubicaciones,id']);
        $this->modalStep = 'rate_check';
        $this->rateInfo = ['message' => 'Verificando tasa de cambio...'];
        $this->verifyExchangeRate();
    }

    // Step 2: Core logic for rate verification
    private function verifyExchangeRate()
    {
        $today = Carbon::today();
        $tasaVigente = TasaDeCambio::where('fecha_vigencia', '<=', $today)
            ->latest('fecha_vigencia')
            ->first();

        if (!$tasaVigente || Carbon::parse($tasaVigente->fecha_vigencia)->isBefore($today)) {
            $this->rateInfo = ['message' => 'Tasa desactualizada. Intentando actualizar desde la API...'];
            $this->dispatch('app-notification-info', ['message' => 'Tasa desactualizada, actualizando...']);

            $nuevaTasa = $this->actualizarTasaDesdeApi();

            if ($nuevaTasa) {
                $fechaVigenciaNueva = Carbon::parse($nuevaTasa->fecha_vigencia);
                if ($fechaVigenciaNueva->isAfter($today)) {
                    $this->originalValidRate = $tasaVigente;
                    $this->rateInfo = [
                        'message' => "La tasa actualizada de la API es para una fecha futura (" . $fechaVigenciaNueva->format('d/m/Y') . ").",
                        'new_rate' => $nuevaTasa->tasa,
                        'old_rate' => $tasaVigente ? $tasaVigente->tasa : 'N/A',
                        'old_rate_date' => $tasaVigente ? Carbon::parse($tasaVigente->fecha_vigencia)->format('d/m/Y') : 'N/A'
                    ];
                    $this->modalStep = 'rate_options';
                } else {
                    $this->completeSetup($nuevaTasa);
                }
            } else {
                $this->rateInfo = ['message' => 'Falló la actualización desde la API. Por favor, ingrese una tasa manualmente para continuar.'];
                $this->modalStep = 'manual_rate_input';
            }
        } else {
            $this->completeSetup($tasaVigente);
        }
    }

    // Step 2a: API update logic (private)
    private function actualizarTasaDesdeApi(): ?TasaDeCambio
    {
        try {
            $response = Http::get('https://bcv-api.rafnixg.dev/rates/');
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['dollar'], $data['date'])) {
                    $nuevaTasaValor = (float) $data['dollar'];
                    $fechaVigencia = Carbon::parse($data['date'])->toDateString();

                    $tasaExistente = TasaDeCambio::whereDate('fecha_vigencia', $fechaVigencia)->first();
                    if ($tasaExistente) {
                        $this->dispatch('app-notification-info', ['message' => 'La tasa de la API para ' . $fechaVigencia . ' ya existía. Usando tasa existente.']);
                        return $tasaExistente;
                    }

                    return TasaDeCambio::create([
                        'moneda' => 'USD',
                        'tasa' => $nuevaTasaValor,
                        'fecha_actualizacion' => Carbon::today(),
                        'fecha_vigencia' => $fechaVigencia,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('BCV API: Connection error', ['exception' => $e->getMessage()]);
        }
        
        $this->dispatch('app-notification-error', ['message' => 'No se pudo obtener la tasa de cambio de la API.']);
        return null;
    }

    // Step 3 (Option A): User chooses to use the old valid rate
    public function useExistingRate()
    {
        if ($this->originalValidRate) {
            $this->completeSetup($this->originalValidRate);
        } else {
            $this->rateInfo = ['message' => 'No se encontró una tasa anterior válida. Ingrese una manualmente.'];
            $this->modalStep = 'manual_rate_input';
        }
    }

    // Step 3 (Option B): User chooses to enter a rate manually
    public function setManualRateMode()
    {
        $this->rateInfo = ['message' => 'Ingrese la tasa de cambio para la fecha de hoy.'];
        $this->modalStep = 'manual_rate_input';
    }

    // Step 4: User saves the manual rate
    public function saveManualRate()
    {
        $this->validate([
            'manualRate' => 'required|numeric|min:0',
            'manualRateDate' => 'required|date',
        ]);

        $tasaExistente = TasaDeCambio::whereDate('fecha_vigencia', $this->manualRateDate)->first();
        if ($tasaExistente) {
            $this->dispatch('app-notification-error', ['message' => 'Ya existe una tasa para la fecha ' . $this->manualRateDate]);
            return;
        }

        $tasa = TasaDeCambio::create([
            'moneda' => 'USD',
            'tasa' => (float) $this->manualRate,
            'fecha_actualizacion' => Carbon::today(),
            'fecha_vigencia' => $this->manualRateDate,
        ]);

        $this->completeSetup($tasa);
    }

    // Final Step: Complete the setup and show the POS
    private function completeSetup(TasaDeCambio $rate)
    {
        $this->activeExchangeRate = $rate;
        
        session(['pos_location_id' => $this->selectedLocationToSet]);
        $this->activeLocationId = $this->selectedLocationToSet;
        $location = Ubicacion::find($this->activeLocationId);
        $this->activeLocationName = $location ? $location->nombre : 'Desconocida';
        
        $this->dispatch('clear-cart');
        $this->locationIsSelected = true;
        $this->modalStep = 'location';

        $this->dispatch('app-notification-success', ['message' => 'Tienda establecida: ' . $this->activeLocationName]);
        $this->dispatch('app-notification-success', ['message' => 'Tasa de cambio fijada en: ' . $this->activeExchangeRate->tasa]);
    }

    public function clearLocation()
    {
        session()->forget('pos_location_id');
        $this->resetModalState();
        $this->dispatch('clear-cart');
    }

    private function resetModalState()
    {
        $this->locationIsSelected = false;
        $this->activeLocationId = null;
        $this->activeLocationName = null;
        $this->modalStep = 'location';
        $this->rateInfo = [];
        $this->originalValidRate = null;
        $this->activeExchangeRate = null;
    }

    #[On('clientSelected')]
    public function clientSelected($clientId)
    {
        if ($clientId) {
            $this->selectedClient = Cliente::find($clientId);
        } else {
            $this->selectedClient = null;
        }
        $this->dispatch('alpine-client-selected', client: $this->selectedClient);
    }

    public function procesarVenta($cartItemsJSON)
    {
        $cartItems = json_decode($cartItemsJSON, true);

        if (empty($cartItems)) {
            $this->dispatch('app-notification-error', ['message' => 'El carrito está vacío.']);
            return;
        }

        if (!$this->activeExchangeRate) {
            $this->dispatch('app-notification-error', ['message' => 'No hay una tasa de cambio activa.']);
            return;
        }

        if (!$this->selectedClient) {
            $this->dispatch('app-notification-error', ['message' => 'Debe seleccionar un cliente para guardar la venta.']);
            return;
        }

        DB::transaction(function () use ($cartItems) {
            // 1. Calculate totals
            $totalUsd = 0;
            foreach ($cartItems as $item) {
                $totalUsd += $item['precio'] * $item['quantity'];
            }
            $totalBs = $totalUsd * $this->activeExchangeRate->tasa;

            // 2. Create FacturaVenta
            $factura = FacturaVenta::create([
                'cliente_id' => $this->selectedClient->id,
                'user_id' => Auth::id(),
                'ubicacion_id' => $this->activeLocationId,
                'tasa_de_cambio_id' => $this->activeExchangeRate->id,
                'total_usd' => $totalUsd,
                'total_bs' => $totalBs,
                'estado' => 'borrador',
                'fecha_borrador' => now(),
            ]);

            // 3. Create FacturaVentaDetalle for each item
            foreach ($cartItems as $item) {
                FacturaVentaDetalle::create([
                    'factura_venta_id' => $factura->id,
                    'producto_id' => $item['id'],
                    'zona_id' => $item['zone_display_id'],
                    'cantidad' => $item['quantity'],
                    'precio_unitario_usd' => $item['precio'],
                    'subtotal_usd' => $item['precio'] * $item['quantity'],
                ]);
            }

            // 4. Dispatch events
            $this->dispatch('app-notification-success', ['message' => "Venta #{$factura->id} guardada como borrador."]);
            $this->dispatch('ventaProcesada');
        });
    }

    public function render()
    {
        return view('livewire.pos-main');
    }
}
