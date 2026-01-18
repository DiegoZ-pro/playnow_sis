@extends('shop.layouts.shop')

@section('title', $product->name . ' - PLAY NOW')

@section('content')
<div class="bg-white" x-data="productDetail()">
    <!-- Breadcrumbs -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex text-sm text-gray-500">
            <a href="{{ route('shop.home') }}" class="hover:text-red-600">Inicio</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.category', $product->category->slug) }}" class="hover:text-red-600">
                {{ $product->category->name }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">{{ $product->name }}</span>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- GALERÍA DE IMÁGENES -->
            <div class="space-y-4">
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                    @if($product->images->isNotEmpty())
                        <img x-bind:src="currentImage" 
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover cursor-zoom-in"
                             x-on:click="openLightbox()">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-image text-gray-300 text-6xl"></i>
                        </div>
                    @endif
                </div>

                @if($product->images->count() > 1)
                <div class="grid grid-cols-5 gap-2">
                    @foreach($product->images as $index => $image)
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer border-2 transition"
                         x-bind:class="currentImageIndex === {{ $index }} ? 'border-red-600' : 'border-transparent hover:border-gray-300'"
                         x-on:click="changeImage({{ $index }}, '{{ asset('storage/' . $image->image_url) }}')">
                        <img src="{{ asset('storage/' . $image->image_url) }}" 
                             alt="Imagen {{ $index + 1 }}"
                             class="w-full h-full object-cover">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- INFORMACIÓN DEL PRODUCTO -->
            <div class="space-y-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-sm text-gray-500 uppercase">{{ $product->brand->name }}</span>
                        @if($product->is_featured)
                        <span class="bg-red-600 text-white text-xs px-2 py-1 rounded">DESTACADO</span>
                        @endif
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl font-bold text-gray-900" x-text="formatPrice(currentPrice)"></span>
                    </div>
                </div>

                @if($product->description)
                <div class="border-t border-b border-gray-200 py-6">
                    <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                </div>
                @endif

                <!-- Selector de Talla -->
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <label class="text-sm font-semibold text-gray-900 uppercase">Selecciona la Talla</label>
                        <button type="button" x-on:click="activeTab = 'sizes'" class="text-sm text-gray-500 hover:text-red-600 underline">
                            Guía de Tallas
                        </button>
                    </div>
                    <div class="grid grid-cols-6 gap-2">
                        @foreach($availableSizes as $size)
                        <button type="button"
                                x-on:click="selectSize({{ $size->id }})"
                                x-bind:class="selectedSize === {{ $size->id }} ? 'border-black bg-black text-white' : 'border-gray-300 hover:border-gray-900'"
                                class="aspect-square border-2 rounded-lg flex items-center justify-center font-semibold transition">
                            {{ $size->value }}
                        </button>
                        @endforeach
                    </div>
                    <!-- Alerta de talla -->
                    <p x-show="showSizeError" x-cloak class="text-red-600 text-sm mt-2 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Por favor selecciona una talla</span>
                    </p>
                </div>

                <!-- Selector de Color -->
                <div>
                    <label class="text-sm font-semibold text-gray-900 uppercase mb-3 block">Selecciona el Color</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach($availableColors as $color)
                        @php
                            $bgStyle = $color->hex_code ? "background-color: {$color->hex_code};" : '';
                        @endphp
                        <button type="button"
                                x-on:click="selectColor({{ $color->id }})"
                                x-bind:class="selectedColor === {{ $color->id }} ? 'ring-2 ring-black ring-offset-2' : 'hover:ring-2 hover:ring-gray-300'"
                                class="relative rounded-lg overflow-hidden transition"
                                title="{{ $color->name }}">
                            @if($color->hex_code)
                            <div class="w-12 h-12 border border-gray-300" style="{{ $bgStyle }}"></div>
                            @else
                            <div class="w-12 h-12 border border-gray-300 bg-gray-100 flex items-center justify-center text-xs font-semibold">
                                {{ substr($color->name, 0, 3) }}
                            </div>
                            @endif
                            <template x-if="selectedColor === {{ $color->id }}">
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
                                    <i class="fas fa-check text-white text-lg"></i>
                                </div>
                            </template>
                        </button>
                        @endforeach
                    </div>
                    <!-- Alerta de color -->
                    <p x-show="showColorError" x-cloak class="text-red-600 text-sm mt-2 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Por favor selecciona un color</span>
                    </p>
                </div>

                <!-- Stock Disponible -->
                <div x-show="variantSelected">
                    <template x-if="currentStock > 0">
                        <div class="flex items-center gap-2 text-green-600">
                            <i class="fas fa-check-circle"></i>
                            <span x-show="currentStock > 5">En stock - Disponible</span>
                            <span x-show="currentStock <= 5" x-text="'¡Solo quedan ' + currentStock + ' unidades!'"></span>
                        </div>
                    </template>
                    <template x-if="currentStock === 0">
                        <div class="flex items-center gap-2 text-red-600">
                            <i class="fas fa-times-circle"></i>
                            <span>Sin stock disponible</span>
                        </div>
                    </template>
                </div>

                <!-- Selector de Cantidad -->
                <div x-show="variantSelected && currentStock > 0">
                    <label class="text-sm font-semibold text-gray-900 uppercase mb-3 block">Cantidad</label>
                    <div class="flex items-center gap-4">
                        <button type="button" 
                                x-on:click="decreaseQuantity()"
                                class="w-10 h-10 border border-gray-300 rounded-lg hover:border-gray-900 flex items-center justify-center">
                            <i class="fas fa-minus text-sm"></i>
                        </button>
                        <input type="number" 
                               x-model.number="quantity" 
                               min="1" 
                               x-bind:max="currentStock"
                               class="w-20 text-center border border-gray-300 rounded-lg py-2 font-semibold">
                        <button type="button" 
                                x-on:click="increaseQuantity()"
                                class="w-10 h-10 border border-gray-300 rounded-lg hover:border-gray-900 flex items-center justify-center">
                            <i class="fas fa-plus text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Botón Agregar al Carrito -->
                <div class="space-y-3 pt-4">
                    <button type="button"
                            x-on:click="addToCart()"
                            class="w-full py-4 rounded-lg text-white font-bold uppercase transition flex items-center justify-center gap-2 bg-black hover:bg-gray-800">
                        <i class="fas" x-bind:class="loading ? 'fa-spinner fa-spin' : 'fa-shopping-cart'"></i>
                        <span x-text="loading ? 'Agregando...' : 'Agregar al Carrito'"></span>
                    </button>
                    
                    <!-- Botón Favoritos -->
                    @auth('customer')
                        <button type="button" 
                                x-on:click="toggleFavorite()"
                                x-bind:disabled="loadingFavorite"
                                x-bind:class="isFavorite ? 'bg-red-600 text-white border-red-600 hover:bg-red-700' : 'border-black text-black hover:bg-gray-50'"
                                class="w-full py-4 border-2 rounded-lg font-bold uppercase transition flex items-center justify-center gap-2">
                            <i class="fas" x-bind:class="loadingFavorite ? 'fa-spinner fa-spin' : 'fa-heart'"></i>
                            <span x-text="loadingFavorite ? 'Procesando...' : (isFavorite ? 'En Favoritos' : 'Agregar a Favoritos')"></span>
                        </button>
                    @else
                        <button type="button" 
                                x-on:click="showLoginModal = true"
                                class="w-full py-4 border-2 border-black rounded-lg text-black font-bold uppercase hover:bg-gray-50 transition flex items-center justify-center gap-2">
                            <i class="far fa-heart"></i>
                            Agregar a Favoritos
                        </button>
                    @endauth
                </div>

                <!-- Información Adicional -->
                <div class="border-t border-gray-200 pt-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-shipping-fast text-xl text-gray-400"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Envío Gratis</p>
                            <p class="text-sm text-gray-600">En compras mayores a Bs 500</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-undo text-xl text-gray-400"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Devoluciones</p>
                            <p class="text-sm text-gray-600">30 días para cambios y devoluciones</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-shield-alt text-xl text-gray-400"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Compra Segura</p>
                            <p class="text-sm text-gray-600">Tus datos están protegidos</p>
                        </div>
                    </div>
                </div>

                <div x-show="variantSelected" class="text-sm text-gray-500">
                    <span>SKU: </span><span x-text="currentSKU" class="font-mono"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs de Información -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 border-t border-gray-200">
        <div class="flex border-b border-gray-200 mb-8">
            <button x-on:click="activeTab = 'details'" 
                    x-bind:class="activeTab === 'details' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-6 py-4 font-semibold border-b-2 transition">
                Detalles del Producto
            </button>
            <button x-on:click="activeTab = 'sizes'" 
                    x-bind:class="activeTab === 'sizes' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-6 py-4 font-semibold border-b-2 transition">
                Guía de Tallas
            </button>
            <button x-on:click="activeTab = 'shipping'" 
                    x-bind:class="activeTab === 'shipping' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-6 py-4 font-semibold border-b-2 transition">
                Envío y Devoluciones
            </button>
        </div>

        <div x-show="activeTab === 'details'" class="prose max-w-none">
            <h3 class="text-xl font-bold mb-4">Características del Producto</h3>
            <ul class="space-y-2">
                <li><strong>Marca:</strong> {{ $product->brand->name }}</li>
                <li><strong>Categoría:</strong> {{ $product->category->name }}</li>
                <li><strong>Material:</strong> Cuero sintético premium</li>
                <li><strong>Suela:</strong> Goma antideslizante</li>
                <li><strong>Estilo:</strong> Deportivo casual</li>
            </ul>
        </div>

        <div x-show="activeTab === 'sizes'" class="prose max-w-none">
            <h3 class="text-xl font-bold mb-4">Guía de Tallas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Talla</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">US</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">EU</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr><td class="px-4 py-3">38</td><td>24.5</td><td>6</td><td>38</td></tr>
                        <tr><td class="px-4 py-3">39</td><td>25</td><td>6.5</td><td>39</td></tr>
                        <tr><td class="px-4 py-3">40</td><td>25.5</td><td>7</td><td>40</td></tr>
                        <tr><td class="px-4 py-3">41</td><td>26</td><td>7.5</td><td>41</td></tr>
                        <tr><td class="px-4 py-3">42</td><td>26.5</td><td>8</td><td>42</td></tr>
                        <tr><td class="px-4 py-3">43</td><td>27</td><td>8.5</td><td>43</td></tr>
                        <tr><td class="px-4 py-3">44</td><td>27.5</td><td>9</td><td>44</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="activeTab === 'shipping'">
            <h3 class="text-xl font-bold mb-4">Envío y Devoluciones</h3>
            <div class="space-y-4">
                <div>
                    <h4 class="font-semibold mb-2">Envío Gratis</h4>
                    <p class="text-gray-600">En compras superiores a Bs 500. Entrega en 3-5 días hábiles.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Devoluciones</h4>
                    <p class="text-gray-600">Tienes 30 días para realizar cambios o devoluciones. El producto debe estar sin usar y con etiquetas.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Garantía</h4>
                    <p class="text-gray-600">Todos nuestros productos cuentan con garantía de 90 días contra defectos de fabricación.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Relacionados -->
    @if($relatedProducts->isNotEmpty())
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-8">También Te Puede Interesar</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                <a href="{{ route('shop.products.show', $related->slug) }}" class="group">
                    <div class="aspect-square bg-white rounded-lg overflow-hidden mb-3">
                        @php
                            $image = $related->images->first();
                        @endphp
                        @if($image)
                        <img src="{{ asset('storage/' . $image->image_url) }}" 
                             alt="{{ $related->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100">
                            <i class="fas fa-image text-gray-300 text-4xl"></i>
                        </div>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-red-600 transition">{{ $related->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $related->brand->name }}</p>
                    <p class="font-bold text-gray-900 mt-1">Bs {{ number_format($related->base_price, 2) }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Lightbox Modal -->
    <div x-show="showLightbox" 
         x-cloak
         x-on:click="showLightbox = false"
         class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
        <div class="relative max-w-5xl w-full">
            <img x-bind:src="currentImage" class="w-full h-auto">
            <button x-on:click="showLightbox = false" 
                    class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Modal Login Requerido -->
    <div x-show="showLoginModal" 
         x-cloak
         x-on:click="showLoginModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6" x-on:click.stop>
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-heart text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Inicia Sesión</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Para agregar productos a favoritos necesitas iniciar sesión o crear una cuenta.
                </p>
                <div class="flex gap-3">
                    <button x-on:click="showLoginModal = false" 
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition">
                        Cancelar
                    </button>
                    <a href="{{ route('shop.login') }}" 
                       class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold text-center transition">
                        Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast de Éxito -->
    <div x-show="showToast" 
         x-cloak
         x-transition
         class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 z-50">
        <i class="fas fa-check-circle text-2xl"></i>
        <div>
            <p class="font-semibold">¡Producto agregado!</p>
            <p class="text-sm">Se agregó al carrito exitosamente</p>
        </div>
    </div>

    <!-- Toast de Favorito -->
    <div x-show="showFavoriteToast" 
         x-cloak
         x-transition
         class="fixed bottom-4 right-4 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 z-50"
         x-bind:class="isFavorite ? 'bg-red-600' : 'bg-gray-600'">
        <i class="fas fa-heart text-2xl"></i>
        <div>
            <p class="font-semibold" x-text="isFavorite ? '¡Agregado a favoritos!' : 'Eliminado de favoritos'"></p>
            <p class="text-sm" x-text="isFavorite ? 'Producto guardado exitosamente' : 'Producto eliminado'"></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productDetail() {
    const productData = {
        id: {{ $product->id }},
        basePrice: {{ $product->base_price }},
        firstImage: '{{ $product->images->first() ? asset("storage/" . $product->images->first()->image_url) : "" }}',
        @auth('customer')
        isFavorite: {{ Auth::guard('customer')->user()->hasFavorite($product->id) ? 'true' : 'false' }}
        @else
        isFavorite: false
        @endauth
    };

    return {
        currentImage: productData.firstImage,
        currentImageIndex: 0,
        showLightbox: false,
        selectedSize: null,
        selectedColor: null,
        variantSelected: false,
        currentVariantId: null,
        currentPrice: productData.basePrice,
        currentStock: 0,
        currentSKU: '',
        quantity: 1,
        activeTab: 'details',
        loading: false,
        showToast: false,
        showSizeError: false,
        showColorError: false,
        isFavorite: productData.isFavorite,
        loadingFavorite: false,
        showFavoriteToast: false,
        showLoginModal: false,

        changeImage(index, imageUrl) {
            this.currentImageIndex = index;
            this.currentImage = imageUrl;
        },

        selectSize(sizeId) {
            this.selectedSize = sizeId;
            this.showSizeError = false;
            this.checkVariant();
        },

        selectColor(colorId) {
            this.selectedColor = colorId;
            this.showColorError = false;
            this.checkVariant();
        },

        decreaseQuantity() {
            if (this.quantity > 1) {
                this.quantity--;
            }
        },

        increaseQuantity() {
            if (this.quantity < this.currentStock) {
                this.quantity++;
            }
        },

        async checkVariant() {
            if (!this.selectedSize || !this.selectedColor) {
                this.variantSelected = false;
                return;
            }

            try {
                const response = await fetch('{{ route("shop.products.variant.info") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productData.id,
                        size_id: this.selectedSize,
                        color_id: this.selectedColor
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.variantSelected = true;
                    this.currentVariantId = data.variant.id;
                    this.currentPrice = data.variant.price;
                    this.currentStock = data.variant.stock;
                    this.currentSKU = data.variant.sku;
                    this.quantity = 1;
                } else {
                    this.variantSelected = false;
                    alert('Esta combinación no está disponible');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al verificar disponibilidad');
            }
        },

        async addToCart() {
            if (!this.selectedSize) {
                this.showSizeError = true;
                document.querySelector('[x-show="showSizeError"]').scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            if (!this.selectedColor) {
                this.showColorError = true;
                document.querySelector('[x-show="showColorError"]').scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            if (!this.variantSelected || this.currentStock === 0) {
                return;
            }

            this.loading = true;

            try {
                const response = await fetch('{{ route("shop.cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        variant_id: this.currentVariantId,
                        quantity: this.quantity
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showToast = true;
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
                    
                    if (window.updateCartCount) {
                        window.updateCartCount();
                    }
                } else {
                    alert(data.message || 'Error al agregar al carrito');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al agregar al carrito');
            } finally {
                this.loading = false;
            }
        },

        async toggleFavorite() {
            this.loadingFavorite = true;

            try {
                const response = await fetch('{{ route("shop.favorites.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productData.id
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.isFavorite = data.is_favorite;
                    this.showFavoriteToast = true;
                    setTimeout(() => {
                        this.showFavoriteToast = false;
                    }, 3000);
                } else {
                    alert(data.message || 'Error al actualizar favoritos');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al actualizar favoritos');
            } finally {
                this.loadingFavorite = false;
            }
        },

        openLightbox() {
            this.showLightbox = true;
        },

        formatPrice(price) {
            return 'Bs ' + parseFloat(price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
    }
}
</script>
@endpush