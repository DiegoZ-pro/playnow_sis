<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Obtener métricas del dashboard
     */
    public function getDashboardMetrics(): array
    {
        return [
            'sales_today' => $this->getSalesToday(),
            'sales_this_month' => $this->getSalesThisMonth(),
            'sales_this_year' => $this->getSalesThisYear(),
            'low_stock_products' => $this->getLowStockCount(),
            'pending_orders' => $this->getPendingOrdersCount(),
            'total_customers' => Customer::count(),
        ];
    }

    /**
     * Obtener ventas del día
     */
    public function getSalesToday(): array
    {
        $sales = Sale::today()->get();

        return [
            'count' => $sales->count(),
            'total' => $sales->sum('total'),
        ];
    }

    /**
     * Obtener ventas del mes
     */
    public function getSalesThisMonth(): array
    {
        $sales = Sale::thisMonth()->get();

        return [
            'count' => $sales->count(),
            'total' => $sales->sum('total'),
        ];
    }

    /**
     * Obtener ventas del año
     */
    public function getSalesThisYear(): array
    {
        $sales = Sale::thisYear()->get();

        return [
            'count' => $sales->count(),
            'total' => $sales->sum('total'),
        ];
    }

    /**
     * Obtener cantidad de productos con stock bajo
     */
    public function getLowStockCount(): int
    {
        return ProductVariant::lowStock()->count();
    }

    /**
     * Obtener cantidad de pedidos pendientes
     */
    public function getPendingOrdersCount(): int
    {
        return \App\Models\Order::pending()->count();
    }

    /**
     * Obtener datos para gráfico de ventas de los últimos N días
     */
    public function getSalesChartData(int $days = 7): array
    {
        $sales = Sale::where('created_at', '>=', Carbon::now()->subDays($days))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $counts = [];
        $totals = [];

        // Llenar todos los días (incluso sin ventas)
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d/m');
            
            $sale = $sales->firstWhere('date', $date);
            $counts[] = $sale ? $sale->count : 0;
            $totals[] = $sale ? (float)$sale->total : 0;
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
            'totals' => $totals,
        ];
    }

    /**
     * Obtener productos más vendidos
     */
    public function getTopSellingProducts(int $limit = 10): array
    {
        return DB::table('sale_details')
            ->join('product_variants', 'sale_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'categories.name as category_name',
                DB::raw('SUM(sale_details.quantity) as total_sold'),
                DB::raw('SUM(sale_details.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Obtener ventas por categoría
     */
    public function getSalesByCategory(): array
    {
        return DB::table('sale_details')
            ->join('product_variants', 'sale_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                DB::raw('COUNT(sale_details.id) as total_items'),
                DB::raw('SUM(sale_details.subtotal) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->toArray();
    }

    /**
     * Obtener reporte de ventas por período
     */
    public function getSalesReport(string $dateFrom, string $dateTo, ?int $userId = null): array
    {
        $query = Sale::with(['details.productVariant.product', 'user'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $sales = $query->get();

        return [
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
            'summary' => [
                'total_sales' => $sales->count(),
                'total_revenue' => $sales->sum('total'),
                'total_discount' => $sales->sum('discount'),
                'average_sale' => $sales->count() > 0 ? $sales->avg('total') : 0,
            ],
            'by_type' => [
                'online' => [
                    'count' => $sales->where('sale_type', 'online')->count(),
                    'total' => $sales->where('sale_type', 'online')->sum('total'),
                ],
                'physical' => [
                    'count' => $sales->where('sale_type', 'physical')->count(),
                    'total' => $sales->where('sale_type', 'physical')->sum('total'),
                ],
            ],
            'by_payment_method' => $this->groupByPaymentMethod($sales),
            'sales' => $sales,
        ];
    }

    /**
     * Obtener reporte de inventario
     */
    public function getInventoryReport(): array
    {
        $variants = ProductVariant::with(['product.category', 'product.brand', 'size', 'color'])
            ->active()
            ->get();

        $totalValue = $variants->sum(function ($variant) {
            return $variant->stock * $variant->price;
        });

        return [
            'summary' => [
                'total_products' => Product::active()->count(),
                'total_variants' => $variants->count(),
                'total_stock' => $variants->sum('stock'),
                'inventory_value' => $totalValue,
                'low_stock_items' => ProductVariant::lowStock()->count(),
                'out_of_stock_items' => $variants->where('stock', 0)->count(),
            ],
            'by_category' => $this->getInventoryByCategory(),
            'low_stock_products' => ProductVariant::with(['product', 'size', 'color'])
                ->lowStock()
                ->get(),
        ];
    }

    /**
     * Obtener clientes frecuentes
     */
    public function getFrequentCustomers(int $limit = 10): array
    {
        return Customer::withCount('sales')
            ->with('sales')
            ->having('sales_count', '>', 0)
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'total_purchases' => $customer->sales_count,
                    'total_spent' => $customer->sales->sum('total'),
                ];
            })
            ->toArray();
    }

    /**
     * Agrupar ventas por método de pago
     */
    private function groupByPaymentMethod($sales): array
    {
        return [
            'cash' => [
                'count' => $sales->where('payment_method', 'cash')->count(),
                'total' => $sales->where('payment_method', 'cash')->sum('total'),
            ],
            'card' => [
                'count' => $sales->where('payment_method', 'card')->count(),
                'total' => $sales->where('payment_method', 'card')->sum('total'),
            ],
            'transfer' => [
                'count' => $sales->where('payment_method', 'transfer')->count(),
                'total' => $sales->where('payment_method', 'transfer')->sum('total'),
            ],
        ];
    }

    /**
     * Obtener inventario por categoría
     */
    private function getInventoryByCategory(): array
    {
        return DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('product_variants.active', true)
            ->select(
                'categories.name',
                DB::raw('COUNT(product_variants.id) as variants_count'),
                DB::raw('SUM(product_variants.stock) as total_stock'),
                DB::raw('SUM(product_variants.stock * product_variants.price) as inventory_value')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->toArray();
    }
}