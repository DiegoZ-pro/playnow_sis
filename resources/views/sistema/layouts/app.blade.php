<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { 
            display: none !important; 
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        
        .sidebar-link.active {
            background-color: #1f2937;
            border-left: 4px solid #ef4444;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-gray-900 text-white transition-all duration-300 flex-shrink-0">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between p-4 border-b border-gray-800">
                    <h1 x-show="sidebarOpen" class="text-xl font-bold text-red-500">PLAY NOW</h1>
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto py-4">
                    <a href="{{ route('sistema.dashboard') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 transition {{ request()->routeIs('sistema.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                    </a>

                    <a href="{{ route('sistema.inventory.index') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 transition {{ request()->routeIs('sistema.inventory.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Inventario</span>
                    </a>

                    <a href="{{ route('sistema.sales.index') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 transition {{ request()->routeIs('sistema.sales.*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Ventas</span>
                    </a>

                    <a href="{{ route('sistema.sales.orders') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 transition {{ request()->routeIs('sistema.sales.orders') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Pedidos Online</span>
                    </a>

                    <a href="{{ route('sistema.customers.index') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 transition {{ request()->routeIs('sistema.customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Clientes</span>
                    </a>

                    <a href="{{ route('sistema.reports.index') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 transition {{ request()->routeIs('sistema.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Reportes</span>
                    </a>

                    @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('sistema.users.index') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 transition {{ request()->routeIs('sistema.users.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3">Usuarios</span>
                    </a>
                    @endif
                </nav>

                <!-- User Menu -->
                <div class="border-t border-gray-800 p-4" x-data="{ userMenuOpen: false }">
                    <div class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center w-full text-gray-300 hover:text-white">
                            <i class="fas fa-user-circle text-2xl w-6"></i>
                            <div x-show="sidebarOpen" class="ml-3 text-left">
                                <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400">{{ auth()->user()->role->name }}</p>
                            </div>
                        </button>

                        <!-- User Dropdown -->
                        <div x-show="userMenuOpen" 
                             @click.away="userMenuOpen = false"
                             x-transition
                             class="absolute bottom-full left-0 mb-2 w-48 bg-white rounded-lg shadow-lg py-2">
                            <form method="POST" action="{{ route('sistema.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between px-6 py-4">
                    <h2 class="text-2xl font-semibold text-gray-800">
                        @yield('title', 'Dashboard')
                    </h2>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications (opcional) -->
                        <button class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-bell text-xl"></i>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Alerts -->
                @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // CSRF Token para AJAX
        window.axios = {
            defaults: {
                headers: {
                    common: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            }
        };
    </script>
    
    @stack('scripts')
</body>
</html>