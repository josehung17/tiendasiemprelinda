<div>
    <form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="{
        precio_compra: {{ old('precio_compra', 0) }},
        margen_ganancia: {{ old('margen_ganancia', 0) }},
        precio_venta: {{ old('precio', 0) }},
        calcularPrecioVenta() {
            let pc = parseFloat(this.precio_compra);
            let mg = parseFloat(this.margen_ganancia);
            if (!isNaN(pc) && !isNaN(mg) && mg < 100) {
                this.precio_venta = (pc / (1 - mg / 100)).toFixed(2);
            } else if (!isNaN(pc)) {
                this.precio_venta = pc.toFixed(2);
            } else {
                this.precio_venta = 0;
            }
        },
        calcularMargenGanancia() {
            let pc = parseFloat(this.precio_compra);
            let pv = parseFloat(this.precio_venta);
            if (!isNaN(pc) && !isNaN(pv) && pv > 0) {
                this.margen_ganancia = ((1 - (pc / pv)) * 100).toFixed(2);
            } else {
                this.margen_ganancia = 0;
            }
        }
    }" x-init="calcularPrecioVenta()">
        @csrf

        <div>
            <x-input-label for="nombre" :value="__('Nombre')" />
            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" required autofocus value="{{ old('nombre') }}" />
            <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="marca_id" :value="__('Marca')" />
                <button type="button" wire:click="openCrearMarcaModal" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    Agregar marca
                </button>
            </div>
            <x-select-input id="marca_id" name="marca_id" class="mt-1 block w-full" required wire:model="marca_id">
                <option value="">{{ __('Selecciona una marca') }}</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                @endforeach
            </x-select-input>
            <x-input-error class="mt-2" :messages="$errors->get('marca_id')" />
        </div>

        <div>
            <x-input-label for="categoria_id" :value="__('Categoría')" />
            <x-select-input id="categoria_id" name="categoria_id" class="mt-1 block w-full" required>
                <option value="">{{ __('Selecciona una categoría') }}</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                @endforeach
            </x-select-input>
            <x-input-error class="mt-2" :messages="$errors->get('categoria_id')" />
        </div>

        <div>
            <x-input-label for="descripcion" :value="__('Descripción')" />
            <x-text-area id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full" placeholder="{{ __('Escribe la descripción aquí...') }}">{{ old('descripcion') }}</x-text-area>
            <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
        </div>

        <div>
            <x-input-label for="stock" :value="__('Stock Inicial')" />
            <x-text-input id="stock" name="stock" type="number" class="mt-1 block w-full" required value="{{ old('stock', 0) }}" />
            <x-input-error class="mt-2" :messages="$errors->get('stock')" />
        </div>

        <div>
            <x-input-label for="precio_compra" :value="__('Precio de Compra')" />
            <x-text-input id="precio_compra" name="precio_compra" type="number" step="0.01" class="mt-1 block w-full" required x-model.number="precio_compra" @input="calcularPrecioVenta" />
            <x-input-error class="mt-2" :messages="$errors->get('precio_compra')" />
        </div>

        <div>
            <x-input-label for="margen_ganancia" :value="__('Margen de Ganancia (%)')" />
            <x-text-input id="margen_ganancia" name="margen_ganancia" type="number" step="0.01" class="mt-1 block w-full" required x-model.number="margen_ganancia" @input="calcularPrecioVenta" />
            <x-input-error class="mt-2" :messages="$errors->get('margen_ganancia')" />
        </div>

        <div>
            <x-input-label for="precio" :value="__('Precio de Venta')" />
            <x-text-input id="precio" name="precio" type="number" step="0.01" class="mt-1 block w-full" required x-model.number="precio_venta" @input="calcularMargenGanancia" />
            <x-input-error class="mt-2" :messages="$errors->get('precio')" />
        </div>

        <div>
            <x-input-label for="precio_descuento" :value="__('Precio con Descuento (opcional)')" />
            <x-text-input id="precio_descuento" name="precio_descuento" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('precio_descuento') }}" />
            <x-input-error class="mt-2" :messages="$errors->get('precio_descuento')" />
        </div>

        <div>
            <x-input-label for="ruta_imagen" :value="__('Imagen del Producto')" />
            <input id="ruta_imagen" name="ruta_imagen" type="file" class="mt-1 block w-full text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-200 dark:hover:file:bg-gray-600" />
            <x-input-error class="mt-2" :messages="$errors->get('ruta_imagen')" />
        </div>

        <div class="mt-4 space-y-2">
            <div class="flex items-center">
                <input id="nuevo" name="nuevo" type="checkbox" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('nuevo') ? 'checked' : '' }}>
                <label for="nuevo" class="ms-2 text-sm text-gray-900 dark:text-gray-100">
                    {{ __('Nuevo Producto') }}
                </label>
            </div>
            <div class="flex items-center">
                <input id="recomendado" name="recomendado" type="checkbox" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('recomendado') ? 'checked' : '' }}>
                <label for="recomendado" class="ms-2 text-sm text-gray-900 dark:text-gray-100">
                    {{ __('Producto Recomendado') }}
                </label>
            </div>
            <div class="flex items-center">
                <input id="descuento" name="descuento" type="checkbox" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('descuento') ? 'checked' : '' }}>
                <label for="descuento" class="ms-2 text-sm text-gray-900 dark:text-gray-100">
                    {{ __('Con Descuento') }}
                </label>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Crear') }}</x-primary-button>
        </div>
    </form>

    @livewire('crear-marca-modal')
</div>
