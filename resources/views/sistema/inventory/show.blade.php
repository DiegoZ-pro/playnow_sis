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
                    <dd>{{ $product->is_featured ? 'Sí' : 'No' }}</dd>
                </div>
            </dl>
        </div>

        <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold text-gray-900 mb-4">Descripción</h4>
            <p class="text-gray-700">{{ $product->description ?? 'Sin descripción' }}</p>
        </div>
    </div>

    <!-- Galería de Imágenes -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6" x-data="imageGallery()">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-semibold text-gray-900">
                Galería de Imágenes
                <span class="ml-2 text-sm text-gray-500">(<span x-text="images.length"></span>/5)</span>
            </h4>
            
            <button x-show="images.length < 5"
                    @click="openUploadModal()" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Agregar Imágenes
            </button>
        </div>

        <!-- Grid de imágenes -->
        <div x-show="images.length > 0" class="grid grid-cols-5 gap-4 mb-4">
            <template x-for="(image, index) in images" :key="image.id">
                <div class="relative group">
                    <!-- Imagen -->
                    <img :src="image.full_url" 
                        :alt="'Imagen ' + (index + 1)"
                        class="w-full h-32 object-cover rounded-lg border-2 cursor-pointer"
                        :class="image.is_primary ? 'border-red-500' : 'border-gray-200'"
                        @click="openLightbox(index)">
                    
                    <!-- Badge de principal -->
                    <div x-show="image.is_primary" 
                        class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded">
                        <i class="fas fa-star"></i> Principal
                    </div>
                    
                    <!-- Orden -->
                    <div class="absolute top-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                        #<span x-text="index + 1"></span>
                    </div>
                    
                    <!-- Overlay con acciones -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 rounded-lg">
                        <!-- Marcar como principal -->
                        <button x-show="!image.is_primary"
                                @click.stop="setPrimary(image.id)"
                                type="button"
                                title="Marcar como principal"
                                class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600">
                            <i class="fas fa-star"></i>
                        </button>
                        
                        <!-- Ver en grande -->
                        <button @click.stop="openLightbox(index)"
                                type="button"
                                title="Ver en grande"
                                class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        
                        <!-- Eliminar -->
                        <button @click.stop="deleteImage(image.id)"
                                type="button"
                                title="Eliminar"
                                class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Sin imágenes -->
        <div x-show="images.length === 0" class="text-center py-12 bg-gray-50 rounded-lg">
            <i class="fas fa-images text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-600 mb-4">No hay imágenes para este producto</p>
            <button @click="openUploadModal()" 
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Agregar Primera Imagen
            </button>
        </div>

        <!-- Modal de subida de imágenes -->
        <div x-show="showUploadModal"
            x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click.self="closeUploadModal()">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold">Agregar Imágenes</h3>
                        <button @click="closeUploadModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>

                    <!-- Dropzone -->
                    <div class="mb-4">
                        <input type="file" 
                            id="imageUpload" 
                            @change="handleFiles($event.target.files)" 
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            multiple
                            class="hidden">
                        
                        <div @click="document.getElementById('imageUpload').click()"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)"
                            :class="isDragging ? 'border-red-500 bg-red-50' : 'border-gray-300'"
                            class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer hover:border-red-500 hover:bg-gray-50 transition">
                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                            <p class="text-lg font-semibold mb-2">Arrastra imágenes aquí o haz clic para seleccionar</p>
                            <p class="text-sm text-gray-500">
                                Puedes agregar <span x-text="5 - images.length"></span> imágenes más
                            </p>
                        </div>
                    </div>

                    <!-- Preview de nuevas imágenes -->
                    <div x-show="newImages.length > 0" class="mb-4">
                        <p class="font-semibold mb-2">Imágenes seleccionadas:</p>
                        <div class="grid grid-cols-3 gap-4">
                            <template x-for="(image, index) in newImages" :key="index">
                                <div class="relative">
                                    <img :src="image.preview" class="w-full h-24 object-cover rounded">
                                    <button @click="removeNewImage(index)"
                                            type="button"
                                            class="absolute top-1 right-1 bg-red-600 text-white p-1 rounded-full hover:bg-red-700">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-3">
                        <button @click="uploadImages()" 
                                :disabled="newImages.length === 0 || uploading"
                                :class="uploading ? 'opacity-50 cursor-not-allowed' : ''"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                            <i class="fas" :class="uploading ? 'fa-spinner fa-spin' : 'fa-upload'"></i>
                            <span x-text="uploading ? ' Subiendo...' : ' Subir Imágenes'"></span>
                        </button>
                        <button @click="closeUploadModal()" 
                                class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Lightbox -->
        <div x-show="showLightbox"
            x-cloak
            class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4"
            @click.self="closeLightbox()"
            @keydown.escape.window="closeLightbox()">
            <div class="relative max-w-5xl w-full">
                <!-- Imagen grande -->
                <img x-show="images[lightboxIndex]" 
                     :src="images[lightboxIndex]?.full_url" 
                     class="w-full h-auto max-h-screen object-contain">
                
                <!-- Botón cerrar -->
                <button @click="closeLightbox()"
                        class="absolute top-4 right-4 bg-white text-black p-3 rounded-full hover:bg-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
                
                <!-- Navegación -->
                <button x-show="lightboxIndex > 0"
                        @click="lightboxIndex--"
                        class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white text-black p-3 rounded-full hover:bg-gray-200">
                    <i class="fas fa-chevron-left text-xl"></i>
                </button>
                
                <button x-show="lightboxIndex < images.length - 1"
                        @click="lightboxIndex++"
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white text-black p-3 rounded-full hover:bg-gray-200">
                    <i class="fas fa-chevron-right text-xl"></i>
                </button>
                
                <!-- Contador -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-75 text-white px-4 py-2 rounded">
                    <span x-text="lightboxIndex + 1"></span> / <span x-text="images.length"></span>
                </div>
            </div>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Razón *</label>
                    <input type="text" x-model="stockData.reason" required placeholder="Ej: Venta en tienda, Inventario físico..." class="w-full border rounded-lg px-3 py-2">
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
// Token CSRF global
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

function showAlert(message, type) {
    const event = new CustomEvent('show-alert', { 
        detail: { message, type } 
    });
    window.dispatchEvent(event);
}

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
            reason: ''
        },

        init() {
            window.addEventListener('show-alert', (e) => {
                this.showAlertInternal(e.detail.message, e.detail.type);
            });
        },

        openVariantModal() {
            this.showVariantModal = true;
        },

        async saveVariant() {
            if (!this.newVariant.size_id || !this.newVariant.color_id) {
                this.showAlertInternal('Debe seleccionar talla y color', 'error');
                return;
            }

            this.showAlertInternal('Guardando variante...', 'loading');

            try {
                const response = await fetch('{{ route("sistema.inventory.variants.add", $product->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.newVariant)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showAlertInternal('¡Variante agregada exitosamente!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.showAlertInternal(result.message || 'Error al agregar variante', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showAlertInternal('Error al procesar la solicitud', 'error');
            }
        },

        openStockModal(variantId, sku, currentStock) {
            this.stockData.variant_id = variantId;
            this.stockData.sku = sku;
            this.stockData.currentStock = currentStock;
            this.stockData.quantity = '';
            this.stockData.reason = '';
            this.showStockModal = true;
        },

        async adjustStock() {
            if (!this.stockData.quantity || this.stockData.quantity < 0) {
                this.showAlertInternal('Debe ingresar una cantidad válida', 'error');
                return;
            }

            if (!this.stockData.reason) {
                this.showAlertInternal('Debe ingresar una razón', 'error');
                return;
            }

            this.showStockModal = false;
            this.showAlertInternal('Ajustando stock...', 'loading');

            try {
                const response = await fetch('{{ route("sistema.inventory.adjust-stock") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.stockData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showAlertInternal('¡Stock actualizado correctamente!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.showAlertInternal(result.message || 'Error al actualizar stock', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showAlertInternal('Error al procesar la solicitud', 'error');
            }
        },

        showAlertInternal(message, type) {
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

function imageGallery() {
    return {
        images: [],
        newImages: [],
        showUploadModal: false,
        showLightbox: false,
        lightboxIndex: 0,
        uploading: false,
        isDragging: false,

        init() {
            // Procesar imágenes de PHP y agregar full_url
            const phpImages = @json($product->images);
            this.images = phpImages.map(img => ({
                ...img,
                full_url: `/storage/${img.image_url}`
            }));
        },

        openUploadModal() {
            if (this.images.length >= 5) {
                showAlert('Ya tienes el máximo de 5 imágenes', 'error');
                return;
            }
            this.showUploadModal = true;
            this.newImages = [];
        },

        closeUploadModal() {
            this.showUploadModal = false;
            this.newImages = [];
        },

        handleDrop(e) {
            this.isDragging = false;
            this.handleFiles(e.dataTransfer.files);
        },

        handleFiles(files) {
            const remainingSlots = 5 - this.images.length - this.newImages.length;
            
            Array.from(files).slice(0, remainingSlots).forEach(file => {
                // Validaciones
                if (file.size > 5242880) {
                    showAlert(`${file.name} excede 5MB`, 'error');
                    return;
                }
                
                if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
                    showAlert(`${file.name} no es válido`, 'error');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.newImages.push({
                        file: file,
                        preview: e.target.result
                    });
                };
                reader.readAsDataURL(file);
            });
        },

        removeNewImage(index) {
            this.newImages.splice(index, 1);
        },

        async uploadImages() {
            if (this.newImages.length === 0) return;
            
            this.uploading = true;
            const formData = new FormData();
            
            this.newImages.forEach((image, index) => {
                formData.append(`images[${index}]`, image.file);
            });

            try {
                const response = await fetch('{{ route("sistema.inventory.images.upload", $product->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    this.closeUploadModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert('Error al subir imágenes', 'error');
            } finally {
                this.uploading = false;
            }
        },

        async deleteImage(imageId) {
            if (!confirm('¿Eliminar esta imagen?')) return;

            try {
                const response = await fetch(`/sistema/inventario/imagenes/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    this.images = this.images.filter(img => img.id !== imageId);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert('Error al eliminar imagen', 'error');
            }
        },

        async setPrimary(imageId) {
            try {
                const response = await fetch(`/sistema/inventario/imagenes/${imageId}/principal`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    this.images.forEach(img => {
                        img.is_primary = (img.id === imageId);
                    });
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert('Error al actualizar imagen principal', 'error');
            }
        },

        openLightbox(index) {
            this.lightboxIndex = index;
            this.showLightbox = true;
        },

        closeLightbox() {
            this.showLightbox = false;
        }
    }
}
</script>
@endpush