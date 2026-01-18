@extends('shop.layouts.shop')

@section('title', 'Productos - PLAY NOW')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <h1 class="text-4xl font-bold mb-8">Nuestros Productos</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Filters Sidebar -->
        <aside class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                <h3 class="text-xl font-bold mb-4">Filtros</h3>
                
                <form method="GET" action="{{ route('shop.products.index') }}">
                    
                    <!-- Categories -->
                    @if(isset($categories) && $categories->count() > 0)
                    <div class="mb-6">
                        <h4 class="font-semibold mb-3">Categorías</h4>
                        @foreach($categories as $category)
                        <label class="flex items-center mb-2">
                            <input type="radio" name="category" value="{{ $category->id }}" 
                                   {{ request('category') == $category->id ? 'checked' : '' }}
                                   class="mr-2">
                            <span>{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @endif

                    <!-- Brands -->
                    @if(isset($brands) && $brands->count() > 0)
                    <div class="mb-6">
                        <h4 class="font-semibold mb-3">Marcas</h4>
                        @foreach($brands as $brand)
                        <label class="flex items-center mb-2">
                            <input type="radio" name="brand" value="{{ $brand->id }}" 
                                   {{ request('brand') == $brand->id ? 'checked' : '' }}
                                   class="mr-2">
                            <span>{{ $brand->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @endif

                    <!-- Sort -->
                    <div class="mb-6">
                        <h4 class="font-semibold mb-3">Ordenar por</h4>
                        <select name="sort" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Más recientes</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nombre A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nombre Z-A</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                        Aplicar Filtros
                    </button>
                    
                    @if(request()->hasAny(['category', 'brand', 'sort']))
                    <a href="{{ route('shop.products.index') }}" class="block text-center mt-3 text-gray-600 hover:text-red-600">
                        Limpiar Filtros
                    </a>
                    @endif
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            
            @if(request('search'))
            <p class="mb-4 text-gray-600">Resultados para: <strong>{{ request('search') }}</strong></p>
            @endif

            @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                    {{-- ✅ CORREGIDO: Usar slug en lugar de id --}}
                    <a href="{{ route('shop.products.show', $product->slug) }}">
                        @if($product->images->first())
                            <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        <div class="p-4">
                            <p class="text-sm text-gray-500 mb-1">{{ $product->brand->name }}</p>
                            <h3 class="font-bold text-lg mb-2 line-clamp-2 group-hover:text-red-600 transition">
                                {{ $product->name }}
                            </h3>
                            <p class="text-red-600 font-bold text-xl">Bs {{ number_format($product->base_price, 2) }}</p>
                            
                            @if($product->variants->where('stock', '>', 0)->count() > 0)
                                <p class="text-sm text-green-600 mt-2">
                                    <i class="fas fa-check-circle"></i> Disponible
                                </p>
                            @else
                                <p class="text-sm text-red-600 mt-2">
                                    <i class="fas fa-times-circle"></i> Agotado
                                </p>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
            
            @else
            <div class="text-center py-16">
                <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                <p class="text-xl text-gray-600">No se encontraron productos</p>
                <a href="{{ route('shop.products.index') }}" class="text-red-600 hover:underline mt-4 inline-block">
                    Ver todos los productos
                </a>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection