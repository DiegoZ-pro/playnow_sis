@extends('shop.layouts.shop')

@section('title', 'Acerca de - PLAY NOW')

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-red-600 to-red-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Acerca de PLAY NOW</h1>
            <p class="text-xl opacity-90">Tu destino para el mejor calzado deportivo y streetwear</p>
        </div>
    </div>

    <!-- Nuestra Historia -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Nuestra Historia</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p>
                        PLAY NOW nació en 2020 con una visión clara: democratizar el acceso a calzado deportivo de calidad 
                        y productos streetwear auténticos en Bolivia.
                    </p>
                    <p>
                        Comenzamos como una pequeña tienda en Cochabamba y, gracias a la confianza de nuestros clientes, 
                        nos hemos expandido para convertirnos en uno de los referentes del calzado deportivo en el país.
                    </p>
                    <p>
                        Hoy, ofrecemos una amplia selección de tenis, gorras y camisetas de las marcas más reconocidas 
                        del mundo, garantizando productos 100% originales y auténticos.
                    </p>
                </div>
            </div>
            <div class="bg-gray-100 rounded-lg h-96 flex items-center justify-center">
                <i class="fas fa-store text-gray-300 text-9xl"></i>
            </div>
        </div>
    </div>

    <!-- Misión y Visión -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Misión -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-bullseye text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Nuestra Misión</h3>
                    </div>
                    <p class="text-gray-700 leading-relaxed">
                        Proporcionar a nuestros clientes productos de calidad superior al mejor precio, 
                        con un servicio excepcional que supere sus expectativas. Nos comprometemos a 
                        ofrecer una experiencia de compra única, tanto en línea como en tienda física.
                    </p>
                </div>

                <!-- Visión -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-eye text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Nuestra Visión</h3>
                    </div>
                    <p class="text-gray-700 leading-relaxed">
                        Ser la tienda líder en Bolivia para calzado deportivo y streetwear, reconocida por 
                        nuestra autenticidad, variedad y excelencia en el servicio al cliente. Aspiramos a 
                        expandirnos a nivel nacional y convertirnos en el destino preferido de los amantes 
                        del deporte y la moda urbana.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Valores -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Nuestros Valores</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Autenticidad -->
            <div class="text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-certificate text-red-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Autenticidad</h3>
                <p class="text-gray-600">
                    Todos nuestros productos son 100% originales. Trabajamos directamente con distribuidores 
                    autorizados para garantizar la autenticidad.
                </p>
            </div>

            <!-- Calidad -->
            <div class="text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-red-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Calidad</h3>
                <p class="text-gray-600">
                    Seleccionamos cuidadosamente cada producto que ofrecemos, asegurando que cumpla con 
                    los más altos estándares de calidad.
                </p>
            </div>

            <!-- Servicio -->
            <div class="text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-red-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Servicio al Cliente</h3>
                <p class="text-gray-600">
                    Nuestro equipo está comprometido en brindarte la mejor experiencia de compra, 
                    antes, durante y después de tu compra.
                </p>
            </div>
        </div>
    </div>

    <!-- Por qué elegirnos -->
    <div class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">¿Por Qué Elegir PLAY NOW?</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <i class="fas fa-shipping-fast text-5xl mb-4 text-red-500"></i>
                    <h3 class="text-lg font-bold mb-2">Envío Gratis</h3>
                    <p class="text-gray-400 text-sm">En compras superiores a Bs 500</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-undo text-5xl mb-4 text-red-500"></i>
                    <h3 class="text-lg font-bold mb-2">Devoluciones Fáciles</h3>
                    <p class="text-gray-400 text-sm">30 días para cambios y devoluciones</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-shield-alt text-5xl mb-4 text-red-500"></i>
                    <h3 class="text-lg font-bold mb-2">Compra Segura</h3>
                    <p class="text-gray-400 text-sm">Protección de datos garantizada</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-headset text-5xl mb-4 text-red-500"></i>
                    <h3 class="text-lg font-bold mb-2">Soporte 24/7</h3>
                    <p class="text-gray-400 text-sm">Estamos aquí para ayudarte</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-white py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">¿Listo para Encontrar tu Estilo?</h2>
            <p class="text-xl text-gray-600 mb-8">
                Explora nuestra colección y descubre los productos perfectos para ti
            </p>
            <a href="{{ route('shop.products.index') }}" 
               class="inline-block bg-red-600 text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-red-700 transition">
                Ver Productos
            </a>
        </div>
    </div>
</div>
@endsection