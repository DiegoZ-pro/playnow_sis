@extends('shop.layouts.shop')

@section('title', 'Pedido Confirmado - PLAY NOW')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Mensaje de Éxito -->
        <div class="bg-white rounded-lg shadow-md p-8 text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-circle text-green-600 text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">¡Pedido Realizado con Éxito!</h1>
            <p class="text-gray-600 mb-6">
                Gracias por tu compra. Tu pedido ha sido registrado y será procesado pronto.
            </p>
            <div class="bg-gray-50 inline-block px-6 py-3 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Número de Pedido</p>
                <p class="text-2xl font-bold text-red-600">#{{ $order->order_number }}</p>
            </div>
        </div>

        <!-- Detalles del Pedido -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Detalles del Pedido</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Información del Cliente -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase mb-3">Información del Cliente</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><i class="fas fa-user w-5 text-gray-400"></i> {{ $order->customer->name }}</p>
                        <p><i class="fas fa-envelope w-5 text-gray-400"></i> {{ $order->customer->email }}</p>
                        @if($order->customer->phone)
                        <p><i class="fas fa-phone w-5 text-gray-400"></i> {{ $order->customer->phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- Dirección de Envío -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase mb-3">Dirección de Envío</h3>
                    <div class="text-gray-700">
                        <p><i class="fas fa-map-marker-alt w-5 text-gray-400"></i> {{ $order->shipping_address }}</p>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase mb-4">Productos</h3>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->details as $detail)
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                            @if($detail->productVariant->product->images->first())
                                            <img src="{{ asset('storage/' . $detail->productVariant->product->images->first()->image_url) }}" 
                                                 alt="{{ $detail->productVariant->product->name }}"
                                                 class="w-full h-full object-cover">
                                            @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-image text-gray-300"></i>
                                            </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $detail->productVariant->product->name }}</p>
                                            <p class="text-sm text-gray-500">
                                                Talla: {{ $detail->productVariant->size->value }} / 
                                                Color: {{ $detail->productVariant->color->name }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center text-gray-900">{{ $detail->quantity }}</td>
                                <td class="px-4 py-4 text-right text-gray-900">Bs {{ number_format($detail->price, 2) }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-gray-900">Bs {{ number_format($detail->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totales -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-end">
                    <div class="w-64 space-y-3">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span>Bs {{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Envío:</span>
                            <span>
                                @if($order->shipping_cost > 0)
                                    Bs {{ number_format($order->shipping_cost, 2) }}
                                @else
                                    <span class="text-green-600 font-semibold">¡GRATIS!</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-2xl font-bold text-gray-900 pt-3 border-t-2 border-gray-300">
                            <span>Total:</span>
                            <span>Bs {{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado del Pedido -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Estado del Pedido</h3>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Pendiente de Confirmación</p>
                    <p class="text-sm text-gray-600">Procesaremos tu pedido en las próximas horas</p>
                </div>
            </div>
        </div>

        <!-- Próximos Pasos -->
        <div class="bg-blue-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-bold text-blue-900 mb-4">
                <i class="fas fa-info-circle mr-2"></i>
                Próximos Pasos
            </h3>
            <ul class="space-y-2 text-blue-800">
                <li><i class="fas fa-check text-blue-600 mr-2"></i> Recibirás un correo de confirmación</li>
                <li><i class="fas fa-check text-blue-600 mr-2"></i> Te contactaremos vía WhatsApp para confirmar tu pedido</li>
                <li><i class="fas fa-check text-blue-600 mr-2"></i> Prepararemos tu pedido para envío</li>
                <li><i class="fas fa-check text-blue-600 mr-2"></i> Recibirás tu pedido en 2-5 días hábiles</li>
            </ul>
        </div>

        <!-- Botones de Acción -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('shop.account.orders') }}" 
               class="inline-block bg-red-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition text-center">
                <i class="fas fa-shopping-bag mr-2"></i>
                Ver Mis Pedidos
            </a>
            <a href="{{ route('shop.products.index') }}" 
               class="inline-block bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition text-center">
                <i class="fas fa-store mr-2"></i>
                Seguir Comprando
            </a>
        </div>
    </div>
</div>
@endsection