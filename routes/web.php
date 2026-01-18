<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sistema\AuthController;
use App\Http\Controllers\Sistema\DashboardController;
use App\Http\Controllers\Sistema\InventoryController;
use App\Http\Controllers\Sistema\SalesController;
use App\Http\Controllers\Sistema\CustomerController;
use App\Http\Controllers\Sistema\ReportController;
use App\Http\Controllers\Sistema\UserController;

// Controllers de la tienda pública
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\Shop\FavoriteController;
use App\Http\Controllers\Shop\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes - Laravel 12 - PLAY NOW
|--------------------------------------------------------------------------
*/

// ============================================
// TIENDA PÚBLICA (www.playnow.com)
// ============================================

// Home y búsqueda
Route::get('/', [HomeController::class, 'index'])->name('shop.home');
Route::get('/buscar', [HomeController::class, 'search'])->name('shop.search');

// Categorías
Route::get('/categoria/{slug}', [HomeController::class, 'category'])->name('shop.category');

// Productos
Route::prefix('productos')->name('shop.products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
    
    // API para obtener info de variante específica
    Route::post('/variante/info', [ProductController::class, 'getVariantInfo'])->name('variant.info');
});

// Carrito
Route::prefix('carrito')->name('shop.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/agregar', [CartController::class, 'add'])->name('add');
    Route::put('/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// Autenticación de clientes
Route::prefix('cuenta')->name('shop.')->group(function () {
    Route::get('/login', [AccountController::class, 'showLoginForm'])->name('login')->middleware('guest');
    Route::post('/login', [AccountController::class, 'login'])->name('login.post');
    Route::get('/registro', [AccountController::class, 'showRegisterForm'])->name('register')->middleware('guest');
    Route::post('/registro', [AccountController::class, 'register'])->name('register.post');
});

// Checkout (SIN middleware - el controller verifica la autenticación internamente)
Route::prefix('checkout')->name('shop.checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'process'])->name('process');
    Route::get('/confirmacion/{order}', [CheckoutController::class, 'confirmation'])->name('confirmation');
});

// Rutas protegidas para clientes (requieren login)
Route::middleware(['auth:customer'])->group(function () {
    
    // Cuenta del cliente
    Route::prefix('mi-cuenta')->name('shop.account.')->group(function () {
        Route::get('/', [AccountController::class, 'profile'])->name('profile');
        Route::put('/', [AccountController::class, 'updateProfile'])->name('update');
        Route::get('/pedidos', [AccountController::class, 'orders'])->name('orders');
        Route::get('/pedidos/{order}', [AccountController::class, 'orderDetail'])->name('order.show');
    });
    
    // Favoritos
    Route::get('/favoritos', [FavoriteController::class, 'index'])->name('shop.favorites.index');
    Route::post('/favoritos/toggle', [FavoriteController::class, 'toggle'])->name('shop.favorites.toggle');
    Route::delete('/favoritos/{productId}', [FavoriteController::class, 'remove'])->name('shop.favorites.remove');
    
    // Logout
    Route::post('/logout', [AccountController::class, 'logout'])->name('shop.logout');
});

// Páginas estáticas
Route::get('/acerca-de', [PageController::class, 'about'])->name('shop.about');
Route::get('/contacto', [PageController::class, 'contact'])->name('shop.contact');
Route::post('/contacto', [PageController::class, 'submitContact'])->name('shop.contact.submit');
Route::get('/terminos-y-condiciones', [PageController::class, 'terms'])->name('shop.terms');
Route::get('/politica-de-privacidad', [PageController::class, 'privacy'])->name('shop.privacy');

// ============================================
// SISTEMA INTERNO (sistema.playnow.com)
// ============================================

// AUTENTICACIÓN
Route::prefix('sistema')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('sistema.login')->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->name('sistema.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('sistema.logout');
});

// SISTEMA - RUTAS PROTEGIDAS
Route::prefix('sistema')->middleware(['auth'])->group(function () {
    
    // DASHBOARD
    Route::get('/', [DashboardController::class, 'index'])->name('sistema.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('sistema.dashboard.index');
    Route::get('/api/metricas', [DashboardController::class, 'getMetrics'])->name('sistema.api.metrics');
    Route::get('/api/graficos', [DashboardController::class, 'getChartData'])->name('sistema.api.charts');

    // INVENTARIO
    Route::prefix('inventario')->name('sistema.inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/crear', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{id}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('destroy');
        
        // Variantes
        Route::post('/{id}/variantes', [InventoryController::class, 'addVariant'])->name('variants.add');
        Route::put('/variantes/{variantId}', [InventoryController::class, 'updateVariant'])->name('variants.update');
        Route::delete('/variantes/{variantId}', [InventoryController::class, 'deleteVariant'])->name('variants.delete');
        
        // Stock
        Route::post('/ajustar-stock', [InventoryController::class, 'adjustStock'])->name('adjust-stock');
        
        // Imágenes
        Route::post('/{id}/imagenes', [InventoryController::class, 'uploadImages'])->name('images.upload');
        Route::delete('/imagenes/{imageId}', [InventoryController::class, 'deleteImage'])->name('images.delete');
        Route::post('/imagenes/{imageId}/principal', [InventoryController::class, 'setPrimaryImage'])->name('images.set-primary');
    });

    // VENTAS
    Route::prefix('ventas')->name('sistema.sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/crear', [SalesController::class, 'create'])->name('create');
        Route::post('/', [SalesController::class, 'store'])->name('store');
        
        // Pedidos online (ANTES de {id} para evitar conflictos)
        Route::get('/pedidos', [SalesController::class, 'orders'])->name('orders');
        Route::post('/pedidos/{id}/confirmar', [SalesController::class, 'confirmOrder'])->name('orders.confirm');
        Route::post('/pedidos/{id}/cancelar', [SalesController::class, 'cancelOrder'])->name('orders.cancel');
        
        // Rutas con parámetros (DESPUÉS de rutas específicas)
        Route::get('/{id}', [SalesController::class, 'show'])->name('show');
        Route::get('/{id}/imprimir', [SalesController::class, 'print'])->name('print');
        
        // API búsquedas
        Route::get('/api/productos', [SalesController::class, 'searchProducts'])->name('api.products');
        Route::get('/api/variante/{variantId}', [SalesController::class, 'getVariant'])->name('api.variant');
        Route::get('/api/clientes', [SalesController::class, 'searchCustomers'])->name('api.customers');
    });

    // CLIENTES
    Route::prefix('clientes')->name('sistema.customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/crear', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
    });

    // REPORTES
    Route::prefix('reportes')->name('sistema.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/ventas', [ReportController::class, 'sales'])->name('sales');
        Route::get('/productos', [ReportController::class, 'products'])->name('products');
        Route::get('/inventario', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/clientes', [ReportController::class, 'customers'])->name('customers');
        
        // API
        Route::get('/api/graficos', [ReportController::class, 'chartData'])->name('api.charts');
        
        // Exportar
        Route::get('/exportar/ventas', [ReportController::class, 'exportSales'])->name('export.sales');
        Route::get('/exportar/inventario', [ReportController::class, 'exportInventory'])->name('export.inventory');
    });

    // USUARIOS (solo admin)
    Route::middleware('check.role:admin')->group(function () {
        Route::prefix('usuarios')->name('sistema.users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/crear', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [UserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        });
    });

});