@extends('shop.layouts.shop')

@section('title', 'PLAY NOW - Tienda de Tenis, Gorras y Camisetas')

@section('content')

<!-- Hero Carousel -->
<section class="relative" x-data="carousel()">
    <div class="relative h-[600px] overflow-hidden">
        <!-- Slides -->
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="currentSlide === index"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0">
                
                <!-- Background Image -->
                <img :src="slide.image" 
                     :alt="slide.title"
                     class="w-full h-full object-cover">
                
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                
                <!-- Content -->
                <div class="absolute inset-0 flex items-center">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl text-white">
                            <p class="text-sm font-bold uppercase tracking-wider mb-2" 
                               x-text="slide.subtitle"></p>
                            <h1 class="text-6xl font-black mb-6 leading-tight" 
                                x-text="slide.title"></h1>
                            <p class="text-xl mb-8" 
                               x-text="slide.description"></p>
                            <a :href="slide.link" 
                               class="inline-block bg-white text-black px-8 py-4 font-bold uppercase tracking-wider hover:bg-red-600 hover:text-white transition">
                                <span x-text="slide.buttonText"></span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Navigation Arrows -->
        <button @click="prevSlide()" 
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 text-black p-4 rounded-full z-10 transition">
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <button @click="nextSlide()" 
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 text-black p-4 rounded-full z-10 transition">
            <i class="fas fa-chevron-right"></i>
        </button>

        <!-- Indicators -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex gap-2 z-10">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="currentSlide = index"
                        :class="currentSlide === index ? 'bg-white w-12' : 'bg-white bg-opacity-50 w-8'"
                        class="h-1 rounded-full transition-all duration-300"></button>
            </template>
        </div>
    </div>
</section>

<!-- Categories Banner -->
<section class="bg-black text-white py-4">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between overflow-x-auto">
            <a href="{{ route('shop.products.index', ['category' => 1]) }}" 
               class="flex items-center gap-2 px-6 py-2 hover:text-red-500 transition whitespace-nowrap">
                <i class="fas fa-shoe-prints text-2xl"></i>
                <span class="font-bold uppercase">Tenis</span>
            </a>
            <a href="{{ route('shop.products.index', ['category' => 2]) }}" 
               class="flex items-center gap-2 px-6 py-2 hover:text-red-500 transition whitespace-nowrap">
                <i class="fas fa-hat-cowboy text-2xl"></i>
                <span class="font-bold uppercase">Gorras</span>
            </a>
            <a href="{{ route('shop.products.index', ['category' => 3]) }}" 
               class="flex items-center gap-2 px-6 py-2 hover:text-red-500 transition whitespace-nowrap">
                <i class="fas fa-tshirt text-2xl"></i>
                <span class="font-bold uppercase">Camisetas</span>
            </a>
            <a href="{{ route('shop.products.index') }}" 
               class="flex items-center gap-2 px-6 py-2 bg-red-600 hover:bg-red-700 transition whitespace-nowrap rounded">
                <span class="font-bold uppercase">Ver Todo</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- New Arrivals by Category -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-black uppercase mb-2">Nuevos Lanzamientos</h2>
            <p class="text-gray-600">Recién llegados a la tienda</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            @php
                // Obtener el producto más reciente de cada categoría
                $latestByCategory = [];
                foreach($categories as $category) {
                    $latestProduct = \App\Models\Product::with(['images', 'category', 'brand', 'variants'])
                        ->where('active', true)
                        ->where('category_id', $category->id)
                        ->latest()
                        ->first();
                    
                    if ($latestProduct) {
                        $latestByCategory[] = $latestProduct;
                    }
                }
            @endphp

            @foreach($latestByCategory as $product)
            <div class="group">
                <a href="{{ route('shop.products.show', $product->id) }}" class="block">
                    <!-- Image Container -->
                    <div class="relative overflow-hidden bg-gray-100 aspect-square mb-4">
                        @if($product->images->first())
                            <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-gray-300 text-6xl"></i>
                            </div>
                        @endif
                        
                        <!-- Badge NUEVO -->
                        <div class="absolute top-4 left-4 bg-black text-white px-3 py-1 text-xs font-bold uppercase">
                            NUEVO
                        </div>

                        <!-- Quick View Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                            <span class="text-white font-bold uppercase opacity-0 group-hover:opacity-100 transition-opacity">
                                Ver Detalles
                            </span>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-1">{{ $product->category->name }}</p>
                        <h3 class="font-bold text-lg mb-2 line-clamp-2 group-hover:text-red-600 transition">
                            {{ $product->name }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-2">{{ $product->brand->name }}</p>
                        <p class="text-xl font-bold">Bs {{ number_format($product->base_price, 2) }}</p>
                    </div>
                </a>
            </div>
            @endforeach

        </div>

        <!-- Ver Más Button -->
        <div class="text-center mt-12">
            <a href="{{ route('shop.products.index') }}" 
               class="inline-block bg-black text-white px-8 py-4 font-bold uppercase hover:bg-red-600 transition">
                Ver Todos Los Productos
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-4xl font-black uppercase">Productos Destacados</h2>
                <p class="text-gray-600 mt-2">Lo mejor de nuestra colección</p>
            </div>
            <a href="{{ route('shop.products.index') }}" 
               class="text-black font-bold uppercase hover:text-red-600 transition flex items-center gap-2">
                Ver Todos
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts->take(4) as $product)
            <div class="group bg-white">
                <a href="{{ route('shop.products.show', $product->id) }}" class="block">
                    <div class="relative overflow-hidden bg-gray-100 aspect-square">
                        @if($product->images->first())
                            <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-gray-300 text-6xl"></i>
                            </div>
                        @endif
                        
                        <div class="absolute top-4 left-4 bg-red-600 text-white px-3 py-1 text-xs font-bold uppercase">
                            Destacado
                        </div>
                    </div>

                    <div class="p-4">
                        <p class="text-xs text-gray-500 uppercase mb-1">{{ $product->category->name }}</p>
                        <h3 class="font-bold text-lg mb-2 line-clamp-2 group-hover:text-red-600 transition">
                            {{ $product->name }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-2">{{ $product->brand->name }}</p>
                        <p class="text-xl font-bold">Bs {{ number_format($product->base_price, 2) }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Promo Banner -->
<section class="relative h-96 bg-gray-900 text-white overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/banners/promo-banner.jpg') }}" 
             alt="Promoción"
             class="w-full h-full object-cover opacity-50"
             onerror="this.style.display='none'">
    </div>
    <div class="relative h-full flex items-center justify-center text-center">
        <div>
            <p class="text-sm font-bold uppercase tracking-wider mb-2">Temporada 2026</p>
            <h2 class="text-6xl font-black mb-6">NUEVA COLECCIÓN</h2>
            <p class="text-xl mb-8">Estilo y rendimiento en cada paso</p>
            <a href="{{ route('shop.products.index') }}" 
               class="inline-block bg-white text-black px-8 py-4 font-bold uppercase hover:bg-red-600 hover:text-white transition">
                Explorar Ahora
            </a>
        </div>
    </div>
</section>

<!-- Categories Grid -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-black uppercase text-center mb-12">Compra por Categoría</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($categories as $category)
            <a href="{{ route('shop.products.index', ['category' => $category->id]) }}" 
               class="relative group overflow-hidden h-96 bg-gray-900">
                <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent z-10"></div>
                <img src="{{ asset('images/categories/' . strtolower($category->name) . '.jpg') }}" 
                     alt="{{ $category->name }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                     onerror="this.style.display='none'">
                <div class="absolute bottom-0 left-0 right-0 p-8 z-20 text-white">
                    <h3 class="text-3xl font-black uppercase mb-2">{{ $category->name }}</h3>
                    <p class="text-sm mb-4">{{ $category->products_count }} productos disponibles</p>
                    <span class="inline-flex items-center gap-2 font-bold uppercase text-sm group-hover:text-red-500 transition">
                        Comprar Ahora
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="bg-black text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-black uppercase mb-4">Únete a la Familia PLAY NOW</h2>
        <p class="text-xl mb-8">Recibe ofertas exclusivas y novedades antes que nadie</p>
        
        <form class="max-w-2xl mx-auto flex gap-4" onsubmit="event.preventDefault(); showAlert('¡Gracias por suscribirte!', 'success');">
            <input type="email" 
                   placeholder="Tu correo electrónico"
                   class="flex-1 px-6 py-4 bg-white text-black focus:outline-none focus:ring-2 focus:ring-red-600"
                   required>
            <button type="submit" 
                    class="bg-red-600 hover:bg-red-700 px-8 py-4 font-bold uppercase transition">
                Suscribirse
            </button>
        </form>
    </div>
</section>

@push('scripts')
<script>
function carousel() {
    return {
        currentSlide: 0,
        slides: [
            {
                image: '{{ asset("images/slides/slide4.jpg") }}',
                subtitle: 'Nueva Colección',
                title: 'DOMINA LA CANCHA',
                description: 'Los mejores tenis deportivos para llevar tu juego al siguiente nivel',
                buttonText: 'Comprar Ahora',
                link: '{{ route("shop.products.index", ["category" => 1]) }}'
            },
            {
                image: '{{ asset("images/slides/slide5.jpg") }}',
                subtitle: 'Estilo Urbano',
                title: 'COMPLETA TU LOOK',
                description: 'Gorras y accesorios que combinan con tu estilo de vida',
                buttonText: 'Ver Colección',
                link: '{{ route("shop.products.index", ["category" => 2]) }}'
            },
            {
                image: '{{ asset("images/slides/slide7.jpg") }}',
                subtitle: 'Tendencias 2026',
                title: 'CAMISETAS PREMIUM',
                description: 'Comodidad y diseño en cada prenda',
                buttonText: 'Descubrir Más',
                link: '{{ route("shop.products.index", ["category" => 3]) }}'
            }
        ],
        
        init() {
            // Auto-advance slides every 5 seconds
            setInterval(() => {
                this.nextSlide();
            }, 5000);
        },
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
        },
        
        prevSlide() {
            this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        }
    }
}
</script>
@endpush

@endsection