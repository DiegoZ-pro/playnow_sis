<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Services\SalesService;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    protected $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    /**
     * Listar ventas
     */
    public function index(Request $request)
    {
        $filters = [
            'sale_type' => $request->input('sale_type'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'user_id' => $request->input('user_id'),
            'per_page' => 20,
        ];

        $sales = $this->salesService->getSales($filters);

        return view('sistema.sales.index', compact('sales'));
    }

    /**
     * Mostrar formulario de crear venta
     */
    public function create()
    {
        $products = Product::with(['variants.size', 'variants.color'])
            ->active()
            ->get();
        
        $customers = Customer::orderBy('name')->get();

        return view('sistema.sales.create', compact('products', 'customers'));
    }

    /**
     * Guardar nueva venta
     */
    public function store(SaleRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id(); // Agregar user_id del usuario autenticado
            
            $sale = $this->salesService->createPhysicalSale($data);

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada exitosamente',
                'sale' => $sale,
                'redirect' => route('sistema.sales.show', $sale->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Mostrar detalle de venta
     */
    public function show($id)
    {
        $sale = $this->salesService->getSaleDetails($id);

        return view('sistema.sales.show', compact('sale'));
    }

    /**
     * Imprimir ticket/boleta
     */
    public function print($id)
    {
        $sale = $this->salesService->getSaleDetails($id);

        return view('sistema.sales.print', compact('sale'));
    }

    /**
     * Listar pedidos online
     */
    public function orders(Request $request)
    {
        $query = Order::with(['customer', 'details']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20);

        return view('sistema.sales.orders', compact('orders'));
    }

    /**
     * Confirmar pedido online
     */
    public function confirmOrder($id)
    {
        try {
            $sale = $this->salesService->confirmOrder($id, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Pedido confirmado y venta creada exitosamente',
                'sale' => $sale,
                'redirect' => route('sistema.sales.show', $sale->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al confirmar pedido: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancelar pedido online
     */
    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $order = $this->salesService->cancelOrder($id, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Pedido cancelado exitosamente',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar pedido: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * API: Buscar productos para venta
     */
    public function searchProducts(Request $request)
    {
        $search = $request->input('search');

        $products = Product::with(['variants.size', 'variants.color'])
            ->where('active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * API: Obtener variante por ID
     */
    public function getVariant($variantId)
    {
        $variant = ProductVariant::with(['product', 'size', 'color'])
            ->findOrFail($variantId);

        return response()->json([
            'success' => true,
            'variant' => $variant,
        ]);
    }

    /**
     * API: Buscar o crear cliente
     */
    public function searchCustomers(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        })
        ->limit(10)
        ->get();

        return response()->json([
            'success' => true,
            'customers' => $customers,
        ]);
    }
}