@extends('sistema.layouts.app')

@section('title', 'Nueva Venta')

@section('content')
<div class="space-y-6" x-data="saleForm()">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Registrar Nueva Venta</h3>
        <a href="{{ route('sistema.sales.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
    </div>

    @if($products->count() == 0)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <p class="font-bold">No hay productos en la base de datos</p>
        <p class="text-sm">Por favor, crea productos desde el módulo de Inventario</p>
    </div>
    @else

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Productos Disponibles -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Buscador Rápido -->
            <div class="bg-white rounded-lg shadow p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Producto</label>
                <input type="text" 
                       x-model="searchText"
                       placeholder="Filtrar por nombre..."
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500">
            </div>

            <!-- Listado de Productos -->
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Productos Disponibles ({{ $products->count() }} productos)</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-96 overflow-y-auto">
                    @foreach($products as $product)
                        @if($product->variants->count() > 0)
                            @foreach($product->variants as $variant)
                            <div x-show="filterProduct('{{ strtolower($product->name) }} {{ strtolower($variant->size->value) }} {{ strtolower($variant->color->name) }}')"
                                 @click="addItemFromList({{ $variant->id }}, '{{ addslashes($product->name) }}', '{{ $variant->size->value }}', '{{ $variant->color->name }}', {{ $variant->price ?? $product->base_price }}, {{ $variant->stock }})"
                                 class="border rounded-lg p-3 hover:bg-gray-50 cursor-pointer transition {{ $variant->stock > 0 ? 'border-gray-300' : 'border-red-300 bg-red-50' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="font-medium text-sm text-gray-900">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-600">{{ $variant->size->value }} / {{ $variant->color->name }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-sm font-semibold text-green-600">Bs {{ number_format($variant->price ?? $product->base_price, 2, ',', '.') }}</span>
                                            <span class="text-xs {{ $variant->stock > 0 ? 'text-blue-600' : 'text-red-600 font-bold' }}">
                                                Stock: {{ $variant->stock }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <button type="button" class="bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-700 transition">
                                            <i class="fas fa-plus text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-3 col-span-2">
                                <p class="text-sm text-yellow-800">
                                    <strong>{{ $product->name }}</strong> no tiene variantes configuradas
                                </p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Items de la Venta -->
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Items de la Venta</h4>
                
                <div class="space-y-2">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex items-center gap-2 p-3 bg-gray-50 rounded">
                            <div class="flex-1">
                                <p class="font-medium text-sm" x-text="item.name"></p>
                                <p class="text-xs text-gray-600" x-text="`Talla: ${item.size} | Color: ${item.color}`"></p>
                            </div>
                            <input type="number" 
                                   x-model="item.quantity"
                                   @change="updateItem(index)"
                                   min="1"
                                   :max="item.stock"
                                   class="w-20 border rounded px-2 py-1 text-sm">
                            <span class="w-24 text-right font-semibold text-sm" x-text="`Bs ${(item.price * item.quantity).toFixed(2)}`"></span>
                            <button @click="removeItem(index)" type="button" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </template>

                    <div x-show="items.length === 0" class="text-center py-8 text-gray-500">
                        <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                        <p>No hay productos agregados</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen y Pago -->
        <div class="space-y-4">
            <!-- Resumen -->
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Resumen</h4>
                
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Subtotal:</dt>
                        <dd class="font-semibold" x-text="`Bs ${subtotal.toFixed(2)}`"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Descuento:</dt>
                        <dd>
                            <input type="number" 
                                   x-model="discount"
                                   @input="calculateTotal()"
                                   min="0"
                                   step="0.01"
                                   placeholder="0.00"
                                   class="w-24 border rounded px-2 py-1 text-sm text-right">
                        </dd>
                    </div>
                    <div class="flex justify-between pt-2 border-t">
                        <dt class="font-bold text-lg">TOTAL:</dt>
                        <dd class="font-bold text-lg text-red-600" x-text="`Bs ${total.toFixed(2)}`"></dd>
                    </div>
                </dl>
            </div>

            <!-- Método de Pago -->
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Método de Pago</h4>
                
                <select x-model="paymentMethod" class="w-full border rounded-lg px-3 py-2 mb-4">
                    <option value="cash">Efectivo</option>
                    <option value="card">Tarjeta</option>
                    <option value="transfer">Transferencia</option>
                </select>

                <button @click="completeSale()" 
                        :disabled="items.length === 0"
                        :class="items.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'"
                        class="w-full text-white py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-check mr-2"></i>Completar Venta
                </button>

                <p x-show="items.length === 0" class="text-xs text-gray-500 text-center mt-2">
                    Agrega productos para continuar
                </p>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div x-show="showConfirmModal" 
         x-cloak
         @click.self="showConfirmModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <i class="fas fa-question-circle text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmar Venta</h3>
                <p class="text-gray-600 mb-1">¿Deseas completar esta venta?</p>
                <p class="text-2xl font-bold text-red-600 mb-6" x-text="`Total: Bs ${total.toFixed(2)}`"></p>
                
                <div class="flex gap-3">
                    <button type="button" 
                            @click="processSale()"
                            class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold">
                        <i class="fas fa-check mr-2"></i>Sí, Confirmar
                    </button>
                    <button type="button" 
                            @click="showConfirmModal = false"
                            class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 font-semibold">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </button>
                </div>
            </div>
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
    @endif
</div>
@endsection

@push('scripts')
<script>
function saleForm() {
    return {
        searchText: '',
        items: [],
        discount: 0,
        subtotal: 0,
        total: 0,
        paymentMethod: 'cash',
        showConfirmModal: false,
        alertVisible: false,
        alertMessage: '',
        alertIcon: '',
        alertClass: '',

        filterProduct(productText) {
            if (!this.searchText) return true;
            return productText.includes(this.searchText.toLowerCase());
        },

        addItemFromList(variantId, productName, size, color, price, stock) {
            if (stock <= 0) {
                alert('Producto sin stock disponible');
                return;
            }

            const existingIndex = this.items.findIndex(item => item.variant_id === variantId);
            
            if (existingIndex !== -1) {
                if (this.items[existingIndex].quantity < stock) {
                    this.items[existingIndex].quantity++;
                } else {
                    alert(`Stock máximo alcanzado: ${stock} unidades`);
                }
            } else {
                this.items.push({
                    variant_id: variantId,
                    name: productName,
                    size: size,
                    color: color,
                    quantity: 1,
                    price: parseFloat(price),
                    stock: stock
                });
            }

            this.calculateTotal();
        },

        updateItem(index) {
            const item = this.items[index];
            if (item.quantity > item.stock) {
                alert(`Stock insuficiente. Disponible: ${item.stock}`);
                item.quantity = item.stock;
            }
            if (item.quantity < 1) {
                item.quantity = 1;
            }
            this.calculateTotal();
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotal();
        },

        calculateTotal() {
            this.subtotal = this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            this.total = this.subtotal - parseFloat(this.discount || 0);
            if (this.total < 0) this.total = 0;
        },

        async completeSale() {
            if (this.items.length === 0) {
                this.showAlert('Debe agregar al menos un producto', 'error');
                return;
            }
            this.showConfirmModal = true;
        },

        async processSale() {
            this.showConfirmModal = false;
            this.showAlert('Procesando venta...', 'loading');

            const data = {
                items: this.items.map(item => ({
                    product_variant_id: item.variant_id,  // ✅ CORREGIDO
                    quantity: item.quantity,
                    unit_price: item.price
                })),
                payment_method: this.paymentMethod,
                discount: this.discount || 0
            };

            try {
                const response = await fetch('{{ route("sistema.sales.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showAlert('¡Venta registrada exitosamente!', 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1500);
                } else {
                    this.showAlert(result.message || 'Error al procesar la venta', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showAlert('Error al procesar la venta. Intenta nuevamente.', 'error');
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