@extends('shop.layouts.shop')

@section('title', 'Contacto - PLAY NOW')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Contáctanos</h1>
            <p class="text-xl text-gray-600">Estamos aquí para ayudarte. Envíanos un mensaje y te responderemos pronto.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Información de Contacto -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Dirección -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-2">Dirección</h3>
                            <p class="text-gray-600 text-sm">
                                Av. América #123<br>
                                Zona Central<br>
                                Cochabamba, Bolivia
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-2">Teléfono</h3>
                            <p class="text-gray-600 text-sm">
                                +591 70123456<br>
                                +591 4 4123456
                            </p>
                            <p class="text-gray-500 text-xs mt-2">Lun - Sáb: 9:00 AM - 8:00 PM</p>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-2">Email</h3>
                            <p class="text-gray-600 text-sm">
                                ventas@playnow.com<br>
                                soporte@playnow.com
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Redes Sociales -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Síguenos</h3>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-pink-600 rounded-full flex items-center justify-center text-white hover:bg-pink-700 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white hover:bg-red-700 transition">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white hover:bg-green-700 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- Horario -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Horarios de Atención</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lunes - Viernes</span>
                            <span class="font-semibold text-gray-900">9:00 AM - 8:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sábado</span>
                            <span class="font-semibold text-gray-900">9:00 AM - 6:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Domingo</span>
                            <span class="font-semibold text-gray-900">10:00 AM - 2:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Contacto -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Envíanos un Mensaje</h2>
                    
                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre Completo *
                                </label>
                                <input type="text" 
                                       name="name" 
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                       placeholder="Tu nombre">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Correo Electrónico *
                                </label>
                                <input type="email" 
                                       name="email" 
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                       placeholder="tu@email.com">
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono
                            </label>
                            <input type="tel" 
                                   name="phone"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                   placeholder="+591 70000000">
                        </div>

                        <!-- Asunto -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Asunto *
                            </label>
                            <select name="subject" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                <option value="">Selecciona un asunto</option>
                                <option value="consulta">Consulta General</option>
                                <option value="pedido">Seguimiento de Pedido</option>
                                <option value="producto">Consulta sobre Producto</option>
                                <option value="devolucion">Cambios y Devoluciones</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <!-- Mensaje -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Mensaje *
                            </label>
                            <textarea name="message" 
                                      required
                                      rows="6"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                      placeholder="Escribe tu mensaje aquí..."></textarea>
                        </div>

                        <!-- Botón Enviar -->
                        <div>
                            <button type="submit" 
                                    class="w-full bg-red-600 text-white px-6 py-4 rounded-lg font-bold text-lg hover:bg-red-700 transition">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Enviar Mensaje
                            </button>
                        </div>

                        <p class="text-sm text-gray-500 text-center">
                            Nos esforzamos por responder todos los mensajes en un plazo de 24 horas.
                        </p>
                    </form>
                </div>

                <!-- Mapa (Opcional) -->
                <div class="mt-8 bg-white rounded-lg shadow-md p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Encuéntranos</h3>
                    <div class="bg-gray-200 rounded-lg h-64 flex items-center justify-center">
                        <p class="text-gray-500">
                            <i class="fas fa-map-marked-alt text-4xl mb-2"></i><br>
                            Mapa de ubicación
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection