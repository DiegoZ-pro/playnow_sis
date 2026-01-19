@extends('shop.layouts.shop')

@section('title', 'Checkout - PLAY NOW')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Finalizar Compra</h1>
            <p class="text-gray-600 mt-2">Completa tus datos de envío para procesar tu pedido</p>
        </div>

        <form method="POST" action="{{ route('shop.checkout.process') }}">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Formulario de Envío -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Información del Cliente -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Información del Cliente</h2>
                        
                        <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                            <p class="text-gray-700">
                                <span class="font-semibold">Nombre:</span> {{ $customer->name }}
                            </p>
                            <p class="text-gray-700">
                                <span class="font-semibold">Email:</span> {{ $customer->email }}
                            </p>
                            @if($customer->phone)
                            <p class="text-gray-700">
                                <span class="font-semibold">Teléfono:</span> {{ $customer->phone }}
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- Dirección de Envío -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Dirección de Envío</h2>
                        
                        @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fas fa-exclamation-circle"></i>
                                <span class="font-semibold">Por favor corrige los siguientes errores:</span>
                            </div>
                            <ul class="list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="space-y-6">
                            <!-- Ciudad -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Ciudad *
                                </label>
                                <select name="shipping_city" 
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                    <option value="">Selecciona una ciudad</option>
                                    <option value="Cochabamba" {{ old('shipping_city', $customer->city ?? '') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                                    <option value="La Paz" {{ old('shipping_city', $customer->city ?? '') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                                    <option value="Santa Cruz" {{ old('shipping_city', $customer->city ?? '') == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                                    <option value="Oruro" {{ old('shipping_city', $customer->city ?? '') == 'Oruro' ? 'selected' : '' }}>Oruro</option>
                                    <option value="Potosí" {{ old('shipping_city', $customer->city ?? '') == 'Potosí' ? 'selected' : '' }}>Potosí</option>
                                    <option value="Sucre" {{ old('shipping_city', $customer->city ?? '') == 'Sucre' ? 'selected' : '' }}>Sucre</option>
                                    <option value="Tarija" {{ old('shipping_city', $customer->city ?? '') == 'Tarija' ? 'selected' : '' }}>Tarija</option>
                                    <option value="Beni" {{ old('shipping_city', $customer->city ?? '') == 'Beni' ? 'selected' : '' }}>Beni</option>
                                    <option value="Pando" {{ old('shipping_city', $customer->city ?? '') == 'Pando' ? 'selected' : '' }}>Pando</option>
                                </select>
                            </div>

                            <!-- Dirección -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Dirección Completa *
                                </label>
                                <textarea name="shipping_address" 
                                          required
                                          rows="3"
                                          placeholder="Calle, número, zona, referencias..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">{{ old('shipping_address', $customer->address ?? '') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Incluye referencias para facilitar la entrega</p>
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Teléfono de Contacto *
                                </label>
                                <input type="tel" 
                                       name="shipping_phone" 
                                       required
                                       value="{{ old('shipping_phone', $customer->phone ?? '') }}"
                                       placeholder="+591 70000000"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>

                            <!-- Notas adicionales -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Notas Adicionales (Opcional)
                                </label>
                                <textarea name="notes" 
                                          rows="3"
                                          placeholder="Instrucciones especiales para la entrega..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen del Pedido -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Resumen del Pedido</h2>
                        
                        <!-- Productos -->
                        <div class="space-y-4 mb-6 max-h-64 overflow-y-auto">
                            @foreach($cart as $item)
                            <div class="flex gap-3">
                                <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                    @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" 
                                         alt="{{ $item['product_name'] }}"
                                         class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-300"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $item['product_name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['size'] }} / {{ $item['color'] }}</p>
                                    <p class="text-xs text-gray-500">Cantidad: {{ $item['quantity'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">Bs {{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Totales -->
                        <div class="border-t border-gray-200 pt-4 space-y-3">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal</span>
                                <span>Bs {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Envío</span>
                                <span>
                                    @if($shippingCost > 0)
                                        Bs {{ number_format($shippingCost, 2) }}
                                    @else
                                        <span class="text-green-600 font-semibold">¡GRATIS!</span>
                                    @endif
                                </span>
                            </div>
                            @if($subtotal >= 500)
                            <div class="bg-green-50 text-green-700 px-3 py-2 rounded text-sm">
                                <i class="fas fa-check-circle mr-1"></i>
                                Has calificado para envío gratis
                            </div>
                            @else
                            <div class="bg-gray-50 text-gray-600 px-3 py-2 rounded text-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                Agrega Bs {{ number_format(500 - $subtotal, 2) }} más para envío gratis
                            </div>
                            @endif
                            <div class="flex justify-between text-xl font-bold text-gray-900 pt-3 border-t-2 border-gray-300">
                                <span>Total</span>
                                <span>Bs {{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <!-- Método de Pago Info -->
                        <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-800 font-semibold mb-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Método de Pago
                            </p>
                            <p class="text-xs text-blue-700">
                                Pago contra entrega. Puedes pagar en efectivo o con tarjeta al recibir tu pedido.
                            </p>
                        </div>

                        <!-- Botón Confirmar -->
                        <button type="submit" 
                                class="w-full bg-red-600 text-white py-4 rounded-lg font-bold text-lg hover:bg-red-700 transition mt-6">
                            <i class="fas fa-check-circle mr-2"></i>
                            Confirmar Pedido
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            Al confirmar aceptas nuestros 
                            <a href="{{ route('shop.terms') }}" class="text-red-600 hover:text-red-700">Términos y Condiciones</a>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection