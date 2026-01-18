@extends('shop.layouts.shop')

@section('title', 'Mis Pedidos - PLAY NOW')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mis Pedidos</h1>
            <p class="text-gray-600 mt-2">Historial completo de tus compras</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <!-- Avatar -->
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3">
                            {{ strtoupper(substr(Auth::guard('customer')->user()->name, 0, 1)) }}
                        </div>
                        <h2 class="font-bold text-gray-900">{{ Auth::guard('customer')->user()->name }}</h2>
                    </div>

                    <!-- Menú -->
                    <nav class="space-y-2">
                        <a href="{{ route('shop.account.profile') }}" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            <i class="far fa-user"></i>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="{{ route('shop.account.orders') }}" 
                           class="flex items-center gap-3 px-4 py-3 bg-red-50 text-red-600 rounded-lg font-semibold">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Mis Pedidos</span>
                        </a>
                        <form method="POST" action="{{ route('shop.logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    </nav>
                </div>
            </div>

            <!-- Listado de Pedidos -->
            <div class="lg:col-span-3">
                @if($orders->count() > 0)
                    <div class="space-y-4">
                        @foreach($orders as $order)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <!-- Header del Pedido -->
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <div class="flex flex-wrap justify-between items-center gap-4">
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-lg">Pedido #{{ $order->order_number }}</h3>
                                        <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-gray-900">Bs {{ number_format($order->total, 2) }}</p>
                                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full mt-1
                                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            @if($order->status === 'pending') Pendiente
                                            @elseif($order->status === 'confirmed') Confirmado
                                            @elseif($order->status === 'shipped') Enviado
                                            @elseif($order->status === 'delivered') Entregado
                                            @elseif($order->status === 'cancelled') Cancelado
                                            @else {{ ucfirst($order->status) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalles del Pedido -->
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($order->details as $detail)
                                    <div class="flex gap-4 items-center">
                                        <!-- Imagen del producto -->
                                        <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                            @if($detail->productVariant->product->images->first())
                                            <img src="{{ asset('storage/' . $detail->productVariant->product->images->first()->image_url) }}" 
                                                 alt="{{ $detail->productVariant->product->name }}"
                                                 class="w-full h-full object-cover">
                                            @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-image text-gray-300 text-2xl"></i>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Info del producto -->
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $detail->productVariant->product->name }}</h4>
                                            <p class="text-sm text-gray-600">
                                                Talla: {{ $detail->productVariant->size->value }} | 
                                                Color: {{ $detail->productVariant->color->name }}
                                            </p>
                                            <p class="text-sm text-gray-500">Cantidad: {{ $detail->quantity }}</p>
                                        </div>

                                        <!-- Precio -->
                                        <div class="text-right">
                                            <p class="font-bold text-gray-900">Bs {{ number_format($detail->subtotal, 2) }}</p>
                                            <p class="text-sm text-gray-500">Bs {{ number_format($detail->price, 2) }} c/u</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Totales -->
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-gray-600">
                                            <span>Subtotal</span>
                                            <span>Bs {{ number_format($order->subtotal, 2) }}</span>
                                        </div>
                                        @if($order->shipping_cost > 0)
                                        <div class="flex justify-between text-gray-600">
                                            <span>Envío</span>
                                            <span>Bs {{ number_format($order->shipping_cost, 2) }}</span>
                                        </div>
                                        @endif
                                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t">
                                            <span>Total</span>
                                            <span>Bs {{ number_format($order->total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dirección de Envío -->
                                @if($order->shipping_address)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <p class="text-sm font-semibold text-gray-700 mb-1">
                                        <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
                                        Dirección de Envío
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $order->shipping_address }}</p>
                                </div>
                                @endif

                                <!-- Botones de Acción -->
                                <div class="mt-6 flex gap-3">
                                    <a href="{{ route('shop.account.order.show', $order->id) }}" 
                                       class="flex-1 bg-red-600 text-white text-center px-4 py-2 rounded-lg hover:bg-red-700 font-semibold transition">
                                        Ver Detalle
                                    </a>
                                    @if($order->status === 'delivered')
                                    <button class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 font-semibold transition">
                                        Comprar Nuevamente
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Paginación -->
                    @if($orders->hasPages())
                    <div class="mt-8">
                        {{ $orders->links() }}
                    </div>
                    @endif

                @else
                    <!-- Sin Pedidos -->
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <i class="fas fa-shopping-bag text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No tienes pedidos aún</h3>
                        <p class="text-gray-600 mb-6">¡Empieza a comprar tus productos favoritos!</p>
                        <a href="{{ route('shop.products.index') }}" 
                           class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-semibold transition">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Ir a Comprar
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection