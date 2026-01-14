<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Sistema</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo -->
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900">PLAY NOW</h1>
                <p class="mt-2 text-sm text-gray-600">Sistema de Gestión</p>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                @yield('content')
            </div>

            <!-- Footer -->
            <p class="text-center text-sm text-gray-500">
                © {{ date('Y') }} PLAY NOW. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>