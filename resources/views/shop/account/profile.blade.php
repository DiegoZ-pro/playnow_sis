@extends('shop.layouts.shop')

@section('title', 'Mi Perfil - PLAY NOW')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mi Cuenta</h1>
            <p class="text-gray-600 mt-2">Administra tu información personal y pedidos</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <!-- Avatar -->
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 bg-red-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $customer->email }}</p>
                    </div>

                    <!-- Menú -->
                    <nav class="space-y-2">
                        <a href="{{ route('shop.account.profile') }}" 
                           class="flex items-center gap-3 px-4 py-3 bg-red-50 text-red-600 rounded-lg font-semibold">
                            <i class="far fa-user"></i>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="{{ route('shop.account.orders') }}" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Mis Pedidos</span>
                        </a>
                        <a href="{{ route('shop.favorites.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-heart"></i>
                            <span>Mis Favoritos</span>
                            @if($favoritesCount > 0)
                            <span class="ml-auto bg-red-600 text-white text-xs px-2 py-1 rounded-full">{{ $favoritesCount }}</span>
                            @endif
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

                <!-- Estadísticas -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Estadísticas</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Pedidos</span>
                            <span class="font-bold text-gray-900">{{ $customer->orders->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Gastado</span>
                            <span class="font-bold text-gray-900">Bs {{ number_format($customer->totalSpent(), 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Favoritos</span>
                            <span class="font-bold text-gray-900">{{ $favoritesCount }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Cliente desde</span>
                            <span class="font-bold text-gray-900">{{ $customer->created_at->format('Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Alertas -->
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span class="font-semibold">Error al actualizar</span>
                    </div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Información Personal -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Información Personal</h2>
                    </div>

                    <form method="POST" action="{{ route('shop.account.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre Completo *
                                </label>
                                <input type="text" 
                                       name="name" 
                                       value="{{ old('name', $customer->name) }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Correo Electrónico *
                                </label>
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email', $customer->email) }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Teléfono
                                </label>
                                <input type="tel" 
                                       name="phone" 
                                       value="{{ old('phone', $customer->phone) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>

                            <!-- Ciudad -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Ciudad
                                </label>
                                <input type="text" 
                                       name="city" 
                                       value="{{ old('city', $customer->city) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>

                            <!-- Dirección -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Dirección
                                </label>
                                <textarea name="address" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">{{ old('address', $customer->address) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" 
                                    class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-semibold transition">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Cambiar Contraseña -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Cambiar Contraseña</h2>

                    <form method="POST" action="{{ route('shop.account.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <!-- Contraseña Actual -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Contraseña Actual
                                </label>
                                <input type="password" 
                                       name="current_password"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>

                            <!-- Nueva Contraseña -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nueva Contraseña
                                </label>
                                <input type="password" 
                                       name="password"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres (dejar en blanco para no cambiar)</p>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmar Nueva Contraseña
                                </label>
                                <input type="password" 
                                       name="password_confirmation"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" 
                                    class="bg-gray-900 text-white px-6 py-3 rounded-lg hover:bg-gray-800 font-semibold transition">
                                <i class="fas fa-key mr-2"></i>
                                Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Pedidos Recientes -->
                @if($recentOrders->isNotEmpty())
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Pedidos Recientes</h2>
                        <a href="{{ route('shop.account.orders') }}" class="text-red-600 hover:text-red-700 font-semibold text-sm">
                            Ver Todos →
                        </a>
                    </div>

                    <div class="space-y-4">
                        @foreach($recentOrders as $order)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-900">Pedido #{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">Bs {{ number_format($order->total, 2) }}</p>
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection