@extends('shop.layouts.shop')

@section('title', 'Términos y Condiciones - PLAY NOW')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Términos y Condiciones</h1>
            <p class="text-gray-600">Última actualización: Enero 2026</p>
        </div>

        <!-- Contenido -->
        <div class="bg-white rounded-lg shadow-md p-8 space-y-8">
            
            <!-- 1. Aceptación -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Aceptación de los Términos</h2>
                <p class="text-gray-700 leading-relaxed">
                    Al acceder y utilizar el sitio web www.playnow.com (en adelante "el Sitio"), 
                    usted acepta estar sujeto a estos Términos y Condiciones de Uso, todas las leyes 
                    y regulaciones aplicables, y acepta que es responsable del cumplimiento de las 
                    leyes locales aplicables. Si no está de acuerdo con alguno de estos términos, 
                    tiene prohibido usar o acceder a este sitio.
                </p>
            </section>

            <!-- 2. Uso del Sitio -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Uso del Sitio Web</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    El contenido del sitio web de PLAY NOW es solo para su información general y uso. 
                    Está sujeto a cambios sin previo aviso.
                </p>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 font-semibold mb-2">Usted se compromete a:</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-700 text-sm">
                        <li>Utilizar el sitio únicamente con fines legales</li>
                        <li>No utilizar el sitio de manera que pueda dañarlo o afectar su disponibilidad</li>
                        <li>No intentar obtener acceso no autorizado al sitio</li>
                        <li>Proporcionar información verdadera y actualizada al registrarse</li>
                        <li>Mantener la confidencialidad de su contraseña</li>
                    </ul>
                </div>
            </section>

            <!-- 3. Productos y Precios -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Productos y Precios</h2>
                <p class="text-gray-700 leading-relaxed mb-3">
                    Todos los productos que se muestran en nuestro sitio web están sujetos a disponibilidad. 
                    Nos reservamos el derecho de descontinuar cualquier producto en cualquier momento.
                </p>
                <p class="text-gray-700 leading-relaxed mb-3">
                    Los precios de nuestros productos están sujetos a cambios sin previo aviso. Nos reservamos 
                    el derecho de modificar o discontinuar cualquier producto sin previo aviso.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    PLAY NOW garantiza que todos los productos ofrecidos son 100% originales y auténticos.
                </p>
            </section>

            <!-- 4. Pedidos -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Proceso de Pedidos</h2>
                <p class="text-gray-700 leading-relaxed mb-3">
                    Al realizar un pedido, usted realiza una oferta para comprar el producto. Nos reservamos 
                    el derecho de aceptar o rechazar su pedido por cualquier motivo.
                </p>
                <div class="space-y-2 text-gray-700">
                    <p><strong>4.1. Confirmación:</strong> Recibirá un correo electrónico de confirmación después de realizar su pedido.</p>
                    <p><strong>4.2. Pago:</strong> El pago debe realizarse al momento de realizar el pedido.</p>
                    <p><strong>4.3. Cancelación:</strong> Puede cancelar su pedido antes de que sea enviado sin cargo alguno.</p>
                </div>
            </section>

            <!-- 5. Envíos -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Política de Envíos</h2>
                <div class="space-y-3 text-gray-700">
                    <p><strong>Tiempos de entrega:</strong></p>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li>Cochabamba: 2-3 días hábiles</li>
                        <li>La Paz y Santa Cruz: 3-5 días hábiles</li>
                        <li>Otras ciudades: 5-7 días hábiles</li>
                    </ul>
                    <p class="mt-3">
                        <strong>Envío Gratis:</strong> En compras superiores a Bs 500 dentro del área metropolitana.
                    </p>
                    <p>
                        Los tiempos de entrega son estimados y pueden variar según las condiciones del servicio de mensajería.
                    </p>
                </div>
            </section>

            <!-- 6. Devoluciones -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Política de Devoluciones y Cambios</h2>
                <p class="text-gray-700 leading-relaxed mb-3">
                    Aceptamos devoluciones y cambios dentro de los 30 días posteriores a la compra, 
                    siempre que el producto esté en su estado original, sin usar y con todas las etiquetas.
                </p>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-sm text-red-800 font-semibold mb-2">Condiciones para devoluciones:</p>
                    <ul class="list-disc list-inside space-y-1 text-red-700 text-sm">
                        <li>El producto debe estar sin usar y en su empaque original</li>
                        <li>Debe incluir todas las etiquetas y accesorios originales</li>
                        <li>Se requiere el comprobante de compra</li>
                        <li>Los gastos de envío de devolución corren por cuenta del cliente (excepto productos defectuosos)</li>
                    </ul>
                </div>
            </section>

            <!-- 7. Garantía -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Garantía</h2>
                <p class="text-gray-700 leading-relaxed">
                    Todos nuestros productos cuentan con una garantía de 90 días contra defectos de fabricación. 
                    Esta garantía no cubre el desgaste normal por uso, daños accidentales o uso inadecuado del producto.
                </p>
            </section>

            <!-- 8. Propiedad Intelectual -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Propiedad Intelectual</h2>
                <p class="text-gray-700 leading-relaxed">
                    Todo el contenido de este sitio web, incluyendo textos, gráficos, logos, imágenes y software, 
                    es propiedad de PLAY NOW o de sus proveedores de contenido y está protegido por las leyes 
                    de derechos de autor bolivianas e internacionales.
                </p>
            </section>

            <!-- 9. Limitación de Responsabilidad -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Limitación de Responsabilidad</h2>
                <p class="text-gray-700 leading-relaxed">
                    PLAY NOW no será responsable por ningún daño directo, indirecto, incidental, especial o 
                    consecuente que resulte del uso o la imposibilidad de usar nuestros productos o servicios, 
                    incluso si se nos ha notificado de la posibilidad de tales daños.
                </p>
            </section>

            <!-- 10. Privacidad -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Protección de Datos</h2>
                <p class="text-gray-700 leading-relaxed">
                    Su privacidad es importante para nosotros. Recopilamos y utilizamos su información personal 
                    únicamente para procesar sus pedidos y mejorar su experiencia de compra.
                </p>
            </section>

            <!-- 11. Modificaciones -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Modificaciones de los Términos</h2>
                <p class="text-gray-700 leading-relaxed">
                    PLAY NOW se reserva el derecho de modificar estos Términos y Condiciones en cualquier momento. 
                    Las modificaciones entrarán en vigor inmediatamente después de su publicación en el sitio web. 
                    Es su responsabilidad revisar estos términos periódicamente.
                </p>
            </section>

            <!-- 12. Ley Aplicable -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Ley Aplicable y Jurisdicción</h2>
                <p class="text-gray-700 leading-relaxed">
                    Estos Términos y Condiciones se regirán e interpretarán de acuerdo con las leyes del 
                    Estado Plurinacional de Bolivia. Cualquier disputa relacionada con estos términos estará 
                    sujeta a la jurisdicción exclusiva de los tribunales de Cochabamba, Bolivia.
                </p>
            </section>

            <!-- 13. Contacto -->
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">13. Contacto</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Si tiene alguna pregunta sobre estos Términos y Condiciones, puede contactarnos:
                </p>
                <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                    <p class="text-gray-700"><i class="fas fa-envelope text-red-600 mr-2"></i> ventas@playnow.com</p>
                    <p class="text-gray-700"><i class="fas fa-phone text-red-600 mr-2"></i> +591 70123456</p>
                    <p class="text-gray-700"><i class="fas fa-map-marker-alt text-red-600 mr-2"></i> Av. América #123, Cochabamba, Bolivia</p>
                </div>
            </section>

            <!-- Footer del documento -->
            <div class="border-t border-gray-200 pt-6 mt-8">
                <p class="text-center text-gray-600 text-sm">
                    Al utilizar nuestro sitio web, usted reconoce que ha leído, entendido y aceptado 
                    estos Términos y Condiciones.
                </p>
                <p class="text-center text-gray-500 text-xs mt-4">
                    © 2026 PLAY NOW. Todos los derechos reservados.
                </p>
            </div>
        </div>

        <!-- Botón de regreso -->
        <div class="text-center mt-8">
            <a href="{{ route('shop.home') }}" 
               class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver a la Tienda
            </a>
        </div>
    </div>
</div>
@endsection