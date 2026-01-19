<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PLAY NOW - Tienda de Tenis, Gorras y Camisetas')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #1f2937;
        }
        ::-webkit-scrollbar-thumb {
            background: #ef4444;
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #dc2626;
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ 
    mobileMenuOpen: false, 
    searchOpen: false,
    cartCount: {{ app(\App\Services\CartService::class)->getCartCount() }}
}">

    <!-- Top Bar -->
    <div class="bg-black text-white text-sm">
        <div class="container mx-auto px-4 py-2 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span><i class="fas fa-phone-alt mr-2"></i>+591 70123456</span>
                <span><i class="fas fa-envelope mr-2"></i>ventas@playnow.com</span>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                @auth('customer')
                    <a href="{{ route('shop.account.profile') }}" class="hover:text-red-500 transition">
                        <i class="far fa-user-circle"></i>
                        <span>Hola, {{ Auth::guard('customer')->user()->name }}</span>
                    </a>
                    <a href="{{ route('shop.account.orders') }}" class="hover:text-red-500 transition">
                        <i class="fas fa-box mr-1"></i>Mis Pedidos
                    </a>
                    {{-- <form method="POST" action="{{ route('shop.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-red-500 transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                        </button>
                    </form> --}}
                @else
                    <a href="{{ route('shop.login') }}" class="hover:text-red-500 transition">
                        <i class="fas fa-sign-in-alt mr-1"></i>Iniciar Sesión
                    </a>
                    <a href="{{ route('shop.register') }}" class="hover:text-red-500 transition">
                        <i class="fas fa-user-plus mr-1"></i>Registrarse
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                
                <!-- Logo -->
                <a href="{{ route('shop.home') }}" class="flex items-center">
                    <img src="{{ asset('images/logo_playnow2.png') }}" alt="PLAY NOW" class="h-12 md:h-16">
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('shop.home') }}" class="text-gray-700 hover:text-red-500 font-semibold transition">
                        Inicio
                    </a>
                    <a href="{{ route('shop.products.index') }}" class="text-gray-700 hover:text-red-500 font-semibold transition">
                        Productos
                    </a>
                    <a href="{{ route('shop.products.index', ['category' => 1]) }}" class="text-gray-700 hover:text-red-500 font-semibold transition">
                        Tenis
                    </a>
                    <a href="{{ route('shop.products.index', ['category' => 2]) }}" class="text-gray-700 hover:text-red-500 font-semibold transition">
                        Gorras
                    </a>
                    <a href="{{ route('shop.products.index', ['category' => 3]) }}" class="text-gray-700 hover:text-red-500 font-semibold transition">
                        Camisetas
                    </a>
                    <a href="{{ route('shop.contact') }}" class="text-gray-700 hover:text-red-500 font-semibold transition">
                        Contacto
                    </a>
                </nav>

                <!-- Search, Cart & Mobile Menu -->
                <div class="flex items-center space-x-4">
                    
                    <!-- Search Button -->
                    <button @click="searchOpen = !searchOpen" class="text-gray-700 hover:text-red-500 transition">
                        <i class="fas fa-search text-xl"></i>
                    </button>

                    <!-- Cart Button -->
                    <a href="{{ route('shop.cart.index') }}" class="relative text-gray-700 hover:text-red-500 transition">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span x-show="cartCount > 0" 
                              x-text="cartCount" 
                              class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                        </span>
                    </a>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-700 hover:text-red-500">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Search Bar (Expandable) -->
            <div x-show="searchOpen" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="pb-4">
                <form action="{{ route('shop.search') }}" method="GET" class="relative">
                    <input type="text" 
                           name="q" 
                           placeholder="Buscar productos..." 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-red-500">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div x-show="mobileMenuOpen" 
             x-cloak
             @click.away="mobileMenuOpen = false"
             class="md:hidden bg-white border-t">
            <nav class="container mx-auto px-4 py-4 space-y-3">
                <a href="{{ route('shop.home') }}" class="block text-gray-700 hover:text-red-500 font-semibold py-2">
                    <i class="fas fa-home mr-2"></i>Inicio
                </a>
                <a href="{{ route('shop.products.index') }}" class="block text-gray-700 hover:text-red-500 font-semibold py-2">
                    <i class="fas fa-shopping-bag mr-2"></i>Productos
                </a>
                <a href="{{ route('shop.products.index', ['category' => 1]) }}" class="block text-gray-700 hover:text-red-500 py-2 pl-6">
                    Tenis
                </a>
                <a href="{{ route('shop.products.index', ['category' => 2]) }}" class="block text-gray-700 hover:text-red-500 py-2 pl-6">
                    Gorras
                </a>
                <a href="{{ route('shop.products.index', ['category' => 3]) }}" class="block text-gray-700 hover:text-red-500 py-2 pl-6">
                    Camisetas
                </a>
                <a href="{{ route('shop.contact') }}" class="block text-gray-700 hover:text-red-500 font-semibold py-2">
                    <i class="fas fa-envelope mr-2"></i>Contacto
                </a>
                
                @auth('customer')
                    <hr class="my-2">
                    <a href="{{ route('shop.account.profile') }}" class="block text-gray-700 hover:text-red-500 py-2">
                        <i class="fas fa-user mr-2"></i>Mi Cuenta
                    </a>
                    <a href="{{ route('shop.account.orders') }}" class="block text-gray-700 hover:text-red-500 py-2">
                        <i class="fas fa-box mr-2"></i>Mis Pedidos
                    </a>
                @else
                    <hr class="my-2">
                    <a href="{{ route('shop.login') }}" class="block text-gray-700 hover:text-red-500 py-2">
                        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                    </a>
                    <a href="{{ route('shop.register') }}" class="block text-gray-700 hover:text-red-500 py-2">
                        <i class="fas fa-user-plus mr-2"></i>Registrarse
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- About -->
                <div>
                    <img src="{{ asset('images/logo_playnowIn_1.png') }}" alt="PLAY NOW" class="h-16 mb-4">
                    <p class="text-gray-400 text-sm">
                        Tu tienda de confianza para tenis deportivos, gorras y camisetas de las mejores marcas.
                    </p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-red-500 transition">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-red-500 transition">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-red-500 transition">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('shop.home') }}" class="text-gray-400 hover:text-red-500 transition">Inicio</a></li>
                        <li><a href="{{ route('shop.products.index') }}" class="text-gray-400 hover:text-red-500 transition">Productos</a></li>
                        <li><a href="{{ route('shop.about') }}" class="text-gray-400 hover:text-red-500 transition">Acerca de</a></li>
                        <li><a href="{{ route('shop.contact') }}" class="text-gray-400 hover:text-red-500 transition">Contacto</a></li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Atención al Cliente</h3>
                    <ul class="space-y-2 text-sm">
                        @auth('customer')
                            <li><a href="{{ route('shop.account.profile') }}" class="text-gray-400 hover:text-red-500 transition">Mi Cuenta</a></li>
                            <li><a href="{{ route('shop.account.orders') }}" class="text-gray-400 hover:text-red-500 transition">Mis Pedidos</a></li>
                        @else
                            <li><a href="{{ route('shop.login') }}" class="text-gray-400 hover:text-red-500 transition">Iniciar Sesión</a></li>
                            <li><a href="{{ route('shop.register') }}" class="text-gray-400 hover:text-red-500 transition">Registrarse</a></li>
                        @endauth
                        <li><a href="{{ route('shop.terms') }}" class="text-gray-400 hover:text-red-500 transition">Términos y Condiciones</a></li>
                        <li><a href="{{ route('shop.privacy') }}" class="text-gray-400 hover:text-red-500 transition">Política de Privacidad</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Contacto</h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-red-500"></i>
                            <span>Av. América, Cochabamba, Bolivia</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-red-500"></i>
                            <span>+591 70123456</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-red-500"></i>
                            <span>ventas@playnow.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-red-500"></i>
                            <span>Lun - Sáb: 9:00 - 20:00</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="border-t border-gray-800">
            <div class="container mx-auto px-4 py-6">
                <div class="text-center text-sm text-gray-400">
                    <p>&copy; {{ date('Y') }} PLAY NOW. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Alert Component -->
    <div id="alert-container" class="fixed top-4 right-4 z-50 space-y-2" style="max-width: 400px;"></div>

    <!-- Global Scripts -->
    <script>
        // CSRF Token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Make functions globally available
        window.showAlert = showAlert;
        window.updateCartCount = updateCartCount;
        window.formatCurrency = formatCurrency;

        // Alert function
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alert-container');
            const alertId = 'alert-' + Date.now();
            
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
            
            const alertHtml = `
                <div id="${alertId}" class="${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 transform transition-all duration-300">
                    <i class="fas ${icon}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            alertContainer.insertAdjacentHTML('beforeend', alertHtml);
            
            setTimeout(() => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }
            }, 3000);
        }

        // ✅ CORREGIDO: Update cart count compatible con Alpine.js v3
        function updateCartCount() {
            fetch('/carrito/count', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Buscar el elemento body que tiene x-data con cartCount
                    const bodyElement = document.querySelector('body[x-data]');
                    if (bodyElement && bodyElement._x_dataStack) {
                        // Alpine.js v3 usa _x_dataStack
                        bodyElement._x_dataStack.forEach(stack => {
                            if (stack.hasOwnProperty('cartCount')) {
                                stack.cartCount = data.count;
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error updating cart count:', error);
            });
        }

        // Format currency
        function formatCurrency(amount) {
            return 'Bs ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&.').replace('.', ',');
        }
    </script>

    @stack('scripts')
</body>
</html>