<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Estado de Cuenta
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Filters and Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label for="cuenta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Seleccionar Cuenta</label>
                            <select id="cuenta" wire:model.live="selectedCuentaId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                @foreach($cuentas as $cuenta)
                                    <option value="{{ $cuenta->id }}">{{ $cuenta->nombre }} ({{ $cuenta->moneda->codigo }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Inicio</label>
                            <input type="date" id="fecha_inicio" wire:model.live="fechaInicio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Fin</label>
                            <input type="date" id="fecha_fin" wire:model.live="fechaFin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <button wire:click="openModal('entrada')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Registrar Entrada
                            </button>
                            <button wire:click="openModal('salida')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">
                                Registrar Salida
                            </button>
                        </div>
                    </div>

                    <!-- Balance Summary -->
                    @if($selectedCuenta)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-center">
                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <h3 class="text-lg font-medium">Saldo Inicial</h3>
                            <p class="text-2xl font-bold @if($saldoInicial < 0) text-red-500 @else text-green-500 @endif">
                                {{ number_format($saldoInicial, 2) }} {{ $selectedCuenta->moneda->codigo }}
                            </p>
                        </div>
                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <h3 class="text-lg font-medium">Saldo Final (Período)</h3>
                            <p class="text-2xl font-bold @if($saldoFinal < 0) text-red-500 @else text-green-500 @endif">
                                {{ number_format($saldoInicial + $movimientos->sum(fn($mov) => $mov->tipo === 'entrada' ? $mov->monto : -$mov->monto), 2) }} {{ $selectedCuenta->moneda->codigo }}
                            </p>
                        </div>
                        <div class="p-4 bg-gray-200 dark:bg-gray-900 rounded-lg">
                            <h3 class="text-lg font-medium">Saldo Total Actual</h3>
                            <p class="text-2xl font-bold @if($saldoFinal < 0) text-red-500 @else text-green-500 @endif">
                                {{ number_format($saldoFinal, 2) }} {{ $selectedCuenta->moneda->codigo }}
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- Movements Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción / Origen</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Entrada</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Salida</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Saldo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @php $runningBalance = $saldoInicial; @endphp
                                @forelse($movimientos as $mov)
                                    @php
                                        $monto = ($mov->tipo === 'entrada' ? $mov->monto : -$mov->monto);
                                        $runningBalance += $monto;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $mov->fecha->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $mov->descripcion }}
                                            @if($mov->origen)
                                                <a href="{{ $mov->origen->getViewUrl() ?? '#' }}" class="text-indigo-600 dark:text-indigo-400 hover:underline ml-2">
                                                    ({{ $mov->origen->getShortClassName() }} #{{ $mov->origen_id }})
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-500">
                                            {{ $mov->tipo === 'entrada' ? number_format($mov->monto, 2) : '' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-500">
                                            {{ $mov->tipo === 'salida' ? number_format($mov->monto, 2) : '' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ number_format($runningBalance, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center">No hay movimientos en el período seleccionado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $movimientos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Movement Modal -->
    @if($showModal)
    <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="guardarMovimientoManual">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                            Registrar {{ $tipoMovimiento === 'entrada' ? 'Entrada' : 'Salida' }} Manual
                        </h3>
                        <div class="mt-4">
                            <div>
                                <label for="monto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Monto</label>
                                <input type="number" step="0.01" wire:model="monto" id="monto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('monto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mt-4">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                                <textarea wire:model="descripcion" id="descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Guardar
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>