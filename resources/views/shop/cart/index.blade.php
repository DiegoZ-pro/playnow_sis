@extends('shop.layouts.shop')

@section('title', 'Carrito de Compras - PLAY NOW')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <h1 class="text-4xl font-bold mb-8">Carrito de Compras</h1>

    <div class="flex flex-col lg:flex-row gap-8" x-data="cartPage()">
        
        <!-- Cart Items -->
        <div class="lg:w-2/3">
            <div x-show="cart.items.length > 0">
                <div class="bg-white rounded-lg shadow-md">
                    <template x-for="item in cart.items" x-bind:key="item.cart_id">
                        <div class="flex items-center gap-4 p-4 border-b last:border-b-0">
                            <!-- Product Image -->
                            <img x-bind:src="item.image.startsWith('http') ? item.image : '/storage/' + item.image" 
                                 x-bind:alt="item.product_name"
                                 class="w-24 h-24 object-cover rounded">
                            
                            <!-- Product Info -->
                            <div class="flex-1">
                                <h3 class="font-bold text-lg" x-text="item.product_name"></h3>
                                <p class="text-sm text-gray-600">
                                    <span x-text="item.size"></span> / <span x-text="item.color"></span>
                                </p>
                                <p class="text-sm text-gray-500">SKU: <span x-text="item.sku"></span></p>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-2">
                                <button x-on:click="updateQuantity(item.cart_id, item.quantity - 1)" 
                                        x-bind:disabled="item.quantity <= 1"
                                        class="bg-gray-200 hover:bg-gray-300 disabled:opacity-50 px-3 py-1 rounded">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="font-bold px-3" x-text="item.quantity"></span>
                                <button x-on:click="updateQuantity(item.cart_id, item.quantity + 1)"
                                        x-bind:disabled="item.quantity >= item.stock"
                                        class="bg-gray-200 hover:bg-gray-300 disabled:opacity-50 px-3 py-1 rounded">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            <!-- Price -->
                            <div class="text-right">
                                <p class="text-lg font-bold text-red-600">Bs <span x-text="parseFloat(item.subtotal).toFixed(2)"></span></p>
                                <p class="text-sm text-gray-500">Bs <span x-text="parseFloat(item.price).toFixed(2)"></span> c/u</p>
                            </div>

                            <!-- Remove Button -->
                            <button x-on:click="openRemoveModal(item.cart_id, item.product_name)" 
                                    class="text-red-600 hover:text-red-800 px-3">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <button x-on:click="openClearModal()" 
                        class="mt-4 text-red-600 hover:text-red-800 font-semibold">
                    <i class="fas fa-trash-alt mr-2"></i>Vaciar Carrito
                </button>
            </div>

            <!-- Empty Cart -->
            <div x-show="cart.items.length === 0" class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
                <p class="text-xl text-gray-600 mb-4">Tu carrito está vacío</p>
                <a href="{{ route('shop.products.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg inline-block">
                    Ir a Comprar
                </a>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24" x-show="cart.items.length > 0">
                <h2 class="text-2xl font-bold mb-6">Resumen del Pedido</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-bold">Bs <span x-text="parseFloat(cart.total).toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Envío</span>
                        <span class="font-bold" x-bind:class="parseFloat(cart.total) >= 500 ? 'text-green-600' : 'text-gray-900'">
                            <span x-text="parseFloat(cart.total) >= 500 ? 'GRATIS' : 'Bs 30.00'"></span>
                        </span>
                    </div>
                    <template x-if="parseFloat(cart.total) >= 500">
                        <div class="bg-green-50 text-green-700 px-3 py-2 rounded text-sm">
                            <i class="fas fa-check-circle mr-1"></i>
                            ¡Has calificado para envío gratis!
                        </div>
                    </template>
                    <template x-if="parseFloat(cart.total) < 500">
                        <div class="bg-gray-50 text-gray-600 px-3 py-2 rounded text-sm">
                            <i class="fas fa-info-circle mr-1"></i>
                            Agrega Bs <span x-text="(500 - parseFloat(cart.total)).toFixed(2)"></span> más para envío gratis
                        </div>
                    </template>
                    <hr>
                    <div class="flex justify-between text-xl">
                        <span class="font-bold">Total</span>
                        <span class="font-bold text-red-600">Bs <span x-text="(parseFloat(cart.total) + (parseFloat(cart.total) >= 500 ? 0 : 30)).toFixed(2)"></span></span>
                    </div>
                </div>

                @auth('customer')
                    <a href="{{ route('shop.checkout.index') }}" 
                       class="block w-full bg-red-600 hover:bg-red-700 text-white text-center px-6 py-3 rounded-lg font-bold mb-3">
                        Proceder al Pago
                    </a>
                @else
                    <a href="{{ route('shop.login') }}" 
                       class="block w-full bg-red-600 hover:bg-red-700 text-white text-center px-6 py-3 rounded-lg font-bold mb-3">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Iniciar Sesión para Comprar
                    </a>
                @endauth

                <a href="{{ route('shop.products.index') }}" 
                   class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 text-center px-6 py-3 rounded-lg font-bold">
                    Seguir Comprando
                </a>
            </div>
        </div>

        <!-- Modal: Confirmar Eliminar Item -->
        <div x-show="showRemoveModal" 
             x-cloak
             x-on:click="closeRemoveModal()"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6" x-on:click.stop>
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-trash text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Eliminar producto?</h3>
                    <p class="text-sm text-gray-600 mb-6">
                        ¿Estás seguro de eliminar <strong x-text="itemToRemove.name"></strong> del carrito?
                    </p>
                    <div class="flex gap-3">
                        <button type="button" 
                                x-on:click="closeRemoveModal()"
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                            Cancelar
                        </button>
                        <button type="button" 
                                x-on:click="confirmRemove()"
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                            <i class="fas fa-trash mr-2"></i>
                            Sí, eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Confirmar Vaciar Carrito -->
        <div x-show="showClearModal" 
             x-cloak
             x-on:click="closeClearModal()"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6" x-on:click.stop>
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Vaciar carrito?</h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Se eliminarán todos los productos del carrito. Esta acción no se puede deshacer.
                    </p>
                    <div class="flex gap-3">
                        <button type="button" 
                                x-on:click="closeClearModal()"
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                            Cancelar
                        </button>
                        <button type="button" 
                                x-on:click="confirmClear()"
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                            <i class="fas fa-trash mr-2"></i>
                            Sí, vaciar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
function cartPage() {
    return {
        cart: {
            items: [],
            total: 0,
            count: 0
        },
        showRemoveModal: false,
        showClearModal: false,
        itemToRemove: {
            id: null,
            name: ''
        },

        init() {
            this.loadCart();
        },

        async loadCart() {
            try {
                const response = await fetch('/carrito', {
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                
                if (response.headers.get('content-type')?.includes('application/json')) {
                    const data = await response.json();
                    this.cart = data;
                }
            } catch (error) {
                console.error('Error loading cart:', error);
            }
        },

        async updateQuantity(cartId, quantity) {
            if (quantity < 1) return;

            try {
                const response = await fetch(`/carrito/${cartId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ quantity })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.cart = data.cart;
                    if (window.updateCartCount) {
                        window.updateCartCount();
                    }
                    if (window.showAlert) {
                        window.showAlert('Cantidad actualizada', 'success');
                    }
                } else {
                    if (window.showAlert) {
                        window.showAlert(data.message, 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                if (window.showAlert) {
                    window.showAlert('Error al actualizar cantidad', 'error');
                }
            }
        },

        // Modal: Abrir confirmación de eliminar item
        openRemoveModal(cartId, productName) {
            this.itemToRemove = {
                id: cartId,
                name: productName
            };
            this.showRemoveModal = true;
        },

        // Modal: Cerrar confirmación de eliminar item
        closeRemoveModal() {
            this.showRemoveModal = false;
            this.itemToRemove = { id: null, name: '' };
        },

        // Modal: Confirmar eliminar item
        async confirmRemove() {
            const cartId = this.itemToRemove.id;
            this.closeRemoveModal();

            try {
                const response = await fetch(`/carrito/${cartId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.cart = data.cart;
                    if (window.updateCartCount) {
                        window.updateCartCount();
                    }
                    if (window.showAlert) {
                        window.showAlert('Producto eliminado', 'success');
                    }
                } else {
                    if (window.showAlert) {
                        window.showAlert(data.message, 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                if (window.showAlert) {
                    window.showAlert('Error al eliminar producto', 'error');
                }
            }
        },

        // Modal: Abrir confirmación de vaciar carrito
        openClearModal() {
            this.showClearModal = true;
        },

        // Modal: Cerrar confirmación de vaciar carrito
        closeClearModal() {
            this.showClearModal = false;
        },

        // Modal: Confirmar vaciar carrito
        async confirmClear() {
            this.closeClearModal();

            try {
                const response = await fetch('/carrito', {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.cart = data.cart;
                    if (window.updateCartCount) {
                        window.updateCartCount();
                    }
                    if (window.showAlert) {
                        window.showAlert('Carrito vaciado', 'success');
                    }
                } else {
                    if (window.showAlert) {
                        window.showAlert(data.message, 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                if (window.showAlert) {
                    window.showAlert('Error al vaciar carrito', 'error');
                }
            }
        }
    }
}
</script>
@endpush
@endsection