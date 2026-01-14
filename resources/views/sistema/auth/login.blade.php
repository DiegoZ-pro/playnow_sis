@extends('sistema.layouts.guest')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">
        Iniciar Sesión
    </h2>

    @if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li class="text-sm">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('sistema.login') }}" class="space-y-6">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Correo Electrónico
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}"
                       class="pl-10 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                       placeholder="tu@email.com"
                       required>
            </div>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Contraseña
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="pl-10 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                       placeholder="••••••••"
                       required>
            </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input type="checkbox" 
                   name="remember" 
                   id="remember" 
                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
            <label for="remember" class="ml-2 block text-sm text-gray-700">
                Recordarme
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full bg-red-600 text-white py-3 rounded-lg font-medium hover:bg-red-700 transition duration-200">
            <i class="fas fa-sign-in-alt mr-2"></i>
            Ingresar al Sistema
        </button>
    </form>

    <!-- Demo Credentials -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm font-medium text-blue-900 mb-2">Credenciales de prueba:</p>
        <div class="text-xs text-blue-700 space-y-1">
            <p><strong>Admin:</strong> admin@playnow.com / admin123</p>
            <p><strong>Vendedor:</strong> vendedor@playnow.com / vendedor123</p>
        </div>
    </div>
</div>
@endsection