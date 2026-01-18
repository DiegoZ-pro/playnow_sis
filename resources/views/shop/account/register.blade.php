@extends('shop.layouts.shop')

@section('title', 'Crear Cuenta - PLAY NOW')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 -mt-20">
    <div class="max-w-md w-full space-y-8">
        
        <!-- Logo -->
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-bold text-gray-900">
                Crear Cuenta
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                O 
                <a href="{{ route('shop.login') }}" class="font-semibold text-red-600 hover:text-red-500">
                    inicia sesión aquí
                </a>
            </p>
        </div>

        <!-- Formulario -->
        <form class="mt-8 space-y-6" method="POST" action="{{ route('shop.register.post') }}">
            @csrf

            @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Errores en el formulario
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
                <!-- Nombre Completo -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre Completo *
                    </label>
                    <input id="name" 
                           name="name" 
                           type="text" 
                           autocomplete="name" 
                           required
                           value="{{ old('name') }}"
                           class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                           placeholder="Juan Pérez">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Correo Electrónico *
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

                <!-- Teléfono -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Teléfono (Opcional)
                    </label>
                    <input id="phone" 
                           name="phone" 
                           type="tel" 
                           autocomplete="tel"
                           value="{{ old('phone') }}"
                           class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                           placeholder="+591 70000000">
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Contraseña *
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="new-password" 
                           required
                           class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                           placeholder="••••••••">
                    <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirmar Contraseña *
                    </label>
                    <input id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           autocomplete="new-password" 
                           required
                           class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                           placeholder="••••••••">
                </div>
            </div>

            <!-- Términos y Condiciones -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="terms" 
                           name="terms" 
                           type="checkbox"
                           required
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms" class="text-gray-700">
                        Acepto los 
                        <a href="{{ route('shop.terms') }}" target="_blank" class="text-red-600 hover:text-red-500">
                            Términos y Condiciones
                        </a>
                        y la 
                        <a href="{{ route('shop.privacy') }}" target="_blank" class="text-red-600 hover:text-red-500">
                            Política de Privacidad
                        </a>
                    </label>
                </div>
            </div>

            <!-- Botón Submit -->
            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 uppercase">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-red-500 group-hover:text-red-400"></i>
                    </span>
                    Crear Cuenta
                </button>
            </div>

            <!-- Link de regreso -->
            <div class="text-center space-y-2">
                <p class="text-sm text-gray-600">
                    ¿Ya tienes cuenta? 
                    <a href="{{ route('shop.login') }}" class="font-semibold text-red-600 hover:text-red-500">
                        Inicia sesión aquí
                    </a>
                </p>
                <a href="{{ route('shop.home') }}" class="block text-sm text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver a la tienda
                </a>
            </div>
        </form>
    </div>
</div>
@endsection