<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('shop.account.profile');
        }

        return view('shop.account.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('customer')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // ✅ SOLUCIÓN SIMPLE: Leer el parámetro redirect si existe
            $redirectUrl = $request->query('redirect', route('shop.home'));
            
            return redirect($redirectUrl)
                ->with('success', '¡Bienvenido de nuevo!');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegisterForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('shop.account.profile');
        }

        return view('shop.account.register');
    }

    /**
     * Procesar registro
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
        ]);

        // Login automático después del registro
        Auth::guard('customer')->login($customer);

        // ✅ Leer el parámetro redirect si existe
        $redirectUrl = $request->query('redirect', route('shop.home'));
        
        return redirect($redirectUrl)
            ->with('success', '¡Cuenta creada exitosamente! Bienvenido a PLAY NOW.');
    }

    /**
     * Mostrar perfil del cliente
     */
    public function profile()
    {
        $customer = Auth::guard('customer')->user();
        
        // Pedidos recientes
        $recentOrders = $customer->orders()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Obtener favoritos recientes
        $favoritesCount = $customer->favorites()->count();
        
        $recentFavorites = $customer->favoriteProducts()
            ->with(['brand', 'images' => function($query) {
                $query->orderBy('is_primary', 'desc')->orderBy('order', 'asc');
            }])
            ->limit(4)
            ->get();

        return view('shop.account.profile', compact('customer', 'recentOrders', 'favoritesCount', 'recentFavorites'));
    }

    /**
     * Actualizar perfil del cliente (incluye cambio de contraseña)
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        // Validación base de información personal
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email,' . $customer->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
        ];

        // Si está intentando cambiar la contraseña
        if ($request->filled('current_password') || $request->filled('password')) {
            $rules['current_password'] = ['required', 'current_password:customer'];
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules);

        // Actualizar información personal
        $customer->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
        ]);

        // Actualizar contraseña si se proporcionó
        if ($request->filled('password')) {
            $customer->update([
                'password' => Hash::make($validated['password']),
            ]);
            return back()->with('success', 'Perfil y contraseña actualizados exitosamente');
        }

        return back()->with('success', 'Perfil actualizado exitosamente');
    }

    /**
     * Mostrar pedidos del cliente
     */
    public function orders()
    {
        $customer = Auth::guard('customer')->user();
        
        $orders = $customer->orders()
            ->with(['details.productVariant.product.images', 'details.productVariant.size', 'details.productVariant.color'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('shop.account.orders', compact('orders'));
    }

    /**
     * Mostrar detalle de un pedido
     */
    public function orderDetail($orderId)
    {
        $customer = Auth::guard('customer')->user();
        
        $order = Order::with(['details.productVariant.product', 'details.productVariant.size', 'details.productVariant.color'])
            ->where('id', $orderId)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        return view('shop.account.order-detail', compact('order'));
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.home')
            ->with('success', 'Sesión cerrada exitosamente');
    }
}