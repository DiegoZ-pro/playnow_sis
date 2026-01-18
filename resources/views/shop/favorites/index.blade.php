@extends('shop.layouts.shop')

@section('title', 'Mis Favoritos - PLAY NOW')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mis Favoritos</h1>
            <p class="text-gray-600 mt-2">Productos que te gustan</p>
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
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Mis Pedidos</span>
                        </a>
                        <a href="{{ route('shop.favorites.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 bg-red-50 text-red-600 rounded-lg font-semibold">
                            <i class="fas fa-heart"></i>
                            <span>Mis Favoritos</span>
                            @if($favorites->total() > 0)
                            <span class="ml-auto bg-red-600 text-white text-xs px-2 py-1 rounded-full">{{ $favorites->total() }}</span>
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
            </div>

            <!-- Contenido de Favoritos -->
            <div class="lg:col-span-3">
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-3 mb-6">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if($favorites->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        @foreach($favorites as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                            <a href="{{ route('shop.products.show', $product->slug) }}" class="block">
                                <div class="aspect-square bg-gray-100 overflow-hidden relative">
                                    @if($product->images->first())
                                        <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" 
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-image text-gray-300 text-4xl"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Botón eliminar favorito -->
                                    <button type="button"
                                            onclick="event.preventDefault(); removeFavorite({{ $product->id }})"
                                            class="absolute top-2 right-2 w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-red-600 hover:bg-red-600 hover:text-white transition">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </a>
                            
                            <div class="p-4">
                                <p class="text-sm text-gray-500 mb-1">{{ $product->brand->name }}</p>
                                <h3 class="font-bold text-lg mb-2 line-clamp-2">
                                    <a href="{{ route('shop.products.show', $product->slug) }}" class="hover:text-red-600">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <p class="text-red-600 font-bold text-xl mb-3">Bs {{ number_format($product->base_price, 2) }}</p>
                                
                                <a href="{{ route('shop.products.show', $product->slug) }}" 
                                   class="block w-full bg-black text-white text-center py-2 rounded-lg hover:bg-gray-800 font-semibold transition">
                                    Ver Producto
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Paginación -->
                    @if($favorites->hasPages())
                    <div class="mt-8">
                        {{ $favorites->links() }}
                    </div>
                    @endif

                @else
                    <!-- Sin Favoritos -->
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <i class="far fa-heart text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No tienes productos favoritos</h3>
                        <p class="text-gray-600 mb-6">Empieza a guardar tus productos favoritos para verlos aquí</p>
                        <a href="{{ route('shop.products.index') }}" 
                           class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-semibold transition">
                            <i class="fas fa-shopping-bag mr-2"></i>
                            Explorar Productos
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function removeFavorite(productId) {
    if (!confirm('¿Estás seguro de eliminar este producto de favoritos?')) {
        return;
    }

    try {
        const response = await fetch(`/favoritos/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar de favoritos');
    }
}
</script>
@endpush
@endsection