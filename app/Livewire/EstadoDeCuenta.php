<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cuenta;
use App\Models\MovimientoCuenta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class EstadoDeCuenta extends Component
{
    use WithPagination;

    public $cuentas = [];
    public $selectedCuentaId;
    public $fechaInicio;
    public $fechaFin;

    // Modal properties
    public $showModal = false;
    public $tipoMovimiento = 'entrada';
    public $monto;
    public $descripcion;

    public function mount()
    {
        $this->cuentas = Cuenta::with('moneda')->get();
        if ($this->cuentas->isNotEmpty()) {
            $this->selectedCuentaId = $this->cuentas->first()->id;
        }
        $this->fechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['selectedCuentaId', 'fechaInicio', 'fechaFin'])) {
            $this->resetPage();
        }
    }

    public function openModal($tipo)
    {
        $this->reset(['monto', 'descripcion']);
        $this->tipoMovimiento = $tipo;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function guardarMovimientoManual()
    {
        $this->validate([
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:255',
            'tipoMovimiento' => 'required|in:entrada,salida',
            'selectedCuentaId' => 'required|exists:cuentas,id',
        ]);

        MovimientoCuenta::create([
            'cuenta_id' => $this->selectedCuentaId,
            'tipo' => $this->tipoMovimiento,
            'monto' => $this->monto,
            'descripcion' => $this->descripcion,
            'responsable_id' => Auth::id(),
            'fecha' => now(),
            'origen_id' => null,
            'origen_type' => null,
        ]);

        $this->closeModal();
        $this->dispatch('app-notification-success', message: 'Movimiento manual guardado exitosamente.');
    }

    public function render()
    {
        $movimientos = collect();
        $saldoInicial = 0;
        $saldoFinal = 0;
        $selectedCuenta = null;

        if ($this->selectedCuentaId) {
            $selectedCuenta = Cuenta::find($this->selectedCuentaId);

            // Calculate initial balance
            $saldoInicial = MovimientoCuenta::where('cuenta_id', $this->selectedCuentaId)
                ->where('fecha', '<', $this->fechaInicio)
                ->get()
                ->sum(function ($mov) {
                    return $mov->tipo === 'entrada' ? $mov->monto : -$mov->monto;
                });

            $query = MovimientoCuenta::with('responsable', 'origen')
                ->where('cuenta_id', $this->selectedCuentaId)
                ->whereBetween('fecha', [$this->fechaInicio, Carbon::parse($this->fechaFin)->endOfDay()])
                ->orderBy('fecha', 'desc');

            $movimientosPaginados = $query->paginate(15);

            // Calculate final balance based on all movements up to the end of the period
            $saldoFinal = MovimientoCuenta::where('cuenta_id', $this->selectedCuentaId)
                ->where('fecha', '<', Carbon::parse($this->fechaFin)->endOfDay())
                ->get()
                ->sum(function ($mov) {
                    return $mov->tipo === 'entrada' ? $mov->monto : -$mov->monto;
                });
        }

        return view('livewire.estado-de-cuenta', [
            'movimientos' => $movimientosPaginados ?? collect(),
            'saldoInicial' => $saldoInicial,
            'saldoFinal' => $saldoFinal,
            'selectedCuenta' => $selectedCuenta,
        ]);
    }
}