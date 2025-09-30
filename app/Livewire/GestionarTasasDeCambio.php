<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TasaDeCambio;
use Livewire\WithPagination;

class GestionarTasasDeCambio extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['app-notification-success' => 'render', 'app-notification-error' => 'render'];

    public function render()
    {
        $tasas = TasaDeCambio::where('moneda', 'like', '%'.$this->search.'%')
            ->orWhere('tasa', 'like', '%'.$this->search.'%')
            ->orWhere('fecha_vigencia', 'like', '%'.$this->search.'%')
            ->orderBy('fecha_vigencia', 'desc')
            ->paginate(10);

        return view('livewire.gestionar-tasas-de-cambio', [
            'tasas' => $tasas,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
