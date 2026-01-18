@extends('shop.layouts.shop')

@section('title', 'Iniciar Sesión - PLAY NOW')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 -mt-20">
    <div class="max-w-md w-full space-y-8">
        
        <!-- Logo -->
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-bold text-gray-900">
                Iniciar Sesión
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                O 
                <a href="{{ route('shop.register') }}" class="font-semibold text-red-600 hover:text-red-500">
                    crea una cuenta nueva
                </a>
            </p>
        </div>

        <!-- Formulario -->
        <form class="mt-8 space-y-6" method="POST" action="{{ route('shop.login.post') }}">
            @csrf

            @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Error al iniciar sesión
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Correo Electrónico
                    </label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required
                           value="{{ old('email') }}"
                           class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                           placeholder="tu@email.com">
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Contraseña
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="current-password" 
                           required
                           class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                           placeholder="••••••••">
                </div>
            </div>

            <!-- Remember me y Forgot password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" 
                           name="remember" 
                           type="checkbox"
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Recordarme
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-red-600 hover:text-red-500">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            </div>

            <!-- Botón Submit -->
            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 uppercase">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-red-500 group-hover:text-red-400"></i>
                    </span>
                    Iniciar Sesión
                </button>
            </div>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gray-50 text-gray-500">
                        ¿Nuevo en PLAY NOW?
                    </span>
                </div>
            </div>

            <!-- Botón Registro -->
            <div>
                <a href="{{ route('shop.register') }}"
                   class="group relative w-full flex justify-center py-3 px-4 border-2 border-gray-300 text-sm font-bold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 uppercase">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-gray-400"></i>
                    </span>
                    Crear Cuenta Nueva
                </a>
            </div>
        </form>

        <!-- Link de regreso -->
        <div class="text-center">
            <a href="{{ route('shop.home') }}" class="text-sm text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver a la tienda
            </a>
        </div>
    </div>
</div>
@endsection