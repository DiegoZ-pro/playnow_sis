@extends('sistema.layouts.app')

@section('title', 'Detalle de Producto')

@section('content')
<div class="space-y-6" x-data="inventoryManager()">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xl font-semibold text-gray-900">{{ $product->name }}</h3>
            <p class="text-sm text-gray-600">{{ $product->category->name }} - {{ $product->brand->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('sistema.inventory.edit', $product->id) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
            <a href="{{ route('sistema.inventory.index') }}" 
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Volver
            </a>
        </div>
    </div>

    <!-- Información del Producto -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold text-gray-900 mb-4">Información General</h4>
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm text-gray-600">Precio Base</dt>
                    <dd class="text-lg font-semibold text-gray-900">Bs {{ number_format($product->base_price, 2, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Estado</dt>
                    <dd>
                        @if($product->active)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Destacado</dt>
                    <dd>{{ $product->featured ? 'Sí' : 'No' }}</dd>
                </div>
            </dl>
        </div>

        <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold text-gray-900 mb-4">Descripción</h4>
            <p class="text-gray-700">{{ $product->description ?? 'Sin descripción' }}</p>
        </div>
    </div>

    <!-- Variantes -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-semibold text-gray-900">Variantes del Producto</h4>
            <button type="button" 
                    @click="openVariantModal()"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                <i class="fas fa-plus mr-2"></i>Agregar Variante
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Talla</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($product->variants as $variant)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-900">{{ $variant->sku }}</td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $variant->size->value }}</td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $variant->color->name }}</td>
                        <td class="px-4 py-4 text-sm text-gray-900 font-medium">
                            Bs {{ number_format($variant->price ?? $product->base_price, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $variant->stock <= $variant->low_stock_threshold ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $variant->stock }} uds
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            @if($variant->active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <button type="button"
                                    @click="openStockModal({{ $variant->id }}, '{{ $variant->sku }}', {{ $variant->stock }})"
                                    class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-box mr-1"></i>Ajustar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            No hay variantes registradas. Click en "Agregar Variante" para crear una.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Agregar Variante -->
    <div x-show="showVariantModal" 
         x-cloak
         @click.self="showVariantModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Agregar Nueva Variante</h3>
            
            <form @submit.prevent="saveVariant()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Talla *</label>
                        <select x-model="newVariant.size_id" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Seleccione...</option>
                            @foreach($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color *</label>
                        <select x-model="newVariant.color_id" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Seleccione...</option>
                            @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Inicial *</label>
                        <input type="number" x-model="newVariant.stock" min="0" required class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio (opcional)</label>
                        <input type="number" x-model="newVariant.price" step="0.01" placeholder="Usar precio base del producto" class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                            Guardar Variante
                        </button>
                        <button type="button" @click="showVariantModal = false" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ajustar Stock -->
    <div x-show="showStockModal" 
         x-cloak
         @click.self="showStockModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Ajustar Stock</h3>
            
            <form @submit.prevent="adjustStock()">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" x-model="stockData.sku" readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Actual</label>
                    <input type="number" x-model="stockData.currentStock" readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Movimiento</label>
                    <select x-model="stockData.type" required class="w-full border rounded-lg px-3 py-2">
                        <option value="entrada">Entrada (agregar stock)</option>
                        <option value="salida">Salida (reducir stock)</option>
                        <option value="ajuste">Ajuste (establecer cantidad exacta)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                    <input type="number" x-model="stockData.quantity" required min="0" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                    <textarea x-model="stockData.notes" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                        Guardar
                    </button>
                    <button type="button" @click="showStockModal = false" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alerta Personalizada -->
    <div x-show="alertVisible" 
         x-cloak
         :class="alertClass"
         class="fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 flex items-center gap-3 min-w-[300px]">
        <div x-html="alertIcon"></div>
        <div x-text="alertMessage" class="flex-1 font-semibold"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function inventoryManager() {
    return {
        showVariantModal: false,
        showStockModal: false,
        alertVisible: false,
        alertMessage: '',
        alertIcon: '',
        alertClass: '',
        
        newVariant: {
            size_id: '',
            color_id: '',
            stock: 0,
            price: ''
        },
        
        stockData: {
            variant_id: null,
            sku: '',
            currentStock: 0,
            type: 'entrada',
            quantity: '',
            notes: ''
        },

        openVariantModal() {
            this.showVariantModal = true;
        },

        async saveVariant() {
            if (!this.newVariant.size_id || !this.newVariant.color_id) {
                this.showAlert('Debe seleccionar talla y color', 'error');
                return;
            }

            this.showAlert('Guardando variante...', 'loading');

            try {
                const response = await fetch('{{ route("sistema.inventory.variants.add", $product->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.newVariant)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showAlert('¡Variante agregada exitosamente!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.showAlert(result.message || 'Error al agregar variante', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showAlert('Error al procesar la solicitud', 'error');
            }
        },

        openStockModal(variantId, sku, currentStock) {
            this.stockData.variant_id = variantId;
            this.stockData.sku = sku;
            this.stockData.currentStock = currentStock;
            this.stockData.quantity = '';
            this.stockData.notes = '';
            this.showStockModal = true;
        },

        async adjustStock() {
            if (!this.stockData.quantity || this.stockData.quantity < 0) {
                this.showAlert('Debe ingresar una cantidad válida', 'error');
                return;
            }

            this.showStockModal = false;
            this.showAlert('Ajustando stock...', 'loading');

            try {
                const response = await fetch('{{ route("sistema.inventory.adjust-stock") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.stockData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showAlert('¡Stock actualizado correctamente!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.showAlert(result.message || 'Error al actualizar stock', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showAlert('Error al procesar la solicitud', 'error');
            }
        },

        showAlert(message, type) {
            this.alertMessage = message;
            
            if (type === 'success') {
                this.alertClass = 'bg-green-500 text-white';
                this.alertIcon = '<i class="fas fa-check-circle text-2xl"></i>';
            } else if (type === 'error') {
                this.alertClass = 'bg-red-500 text-white';
                this.alertIcon = '<i class="fas fa-times-circle text-2xl"></i>';
            } else if (type === 'loading') {
                this.alertClass = 'bg-blue-500 text-white';
                this.alertIcon = '<i class="fas fa-spinner fa-spin text-2xl"></i>';
            }
            
            this.alertVisible = true;
            
            if (type !== 'loading') {
                setTimeout(() => {
                    this.alertVisible = false;
                }, 3000);
            }
        }
    }
}
</script>
@endpush