<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class SalesService
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Crear una venta física
     */
    public function createPhysicalSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            // Validar stock antes de crear la venta
            $this->validateStock($data['items']);

            // Crear o buscar cliente si se proporciona
            $customerId = null;
            if (isset($data['customer'])) {
                $customer = $this->findOrCreateCustomer($data['customer']);
                $customerId = $customer->id;
            }

            // Calcular totales
            $subtotal = $this->calculateSubtotal($data['items']);
            $discount = $data['discount'] ?? 0;
            $total = $subtotal - $discount;

            // Crear venta
            $sale = Sale::create([
                'user_id' => $data['user_id'],
                'customer_id' => $customerId,
                'sale_number' => Sale::generateSaleNumber(),
                'sale_type' => 'physical',
                'payment_method' => $data['payment_method'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
            ]);

            // Crear detalles y reducir stock
            foreach ($data['items'] as $item) {
                $this->addSaleDetail($sale->id, $item);
                
                // Reducir stock
                $this->inventoryService->reduceStock(
                    $item['product_variant_id'],
                    $item['quantity'],
                    $data['user_id'],
                    $sale->sale_number
                );
            }

            return $sale->load(['details.productVariant', 'customer', 'user']);
        });
    }

    /**
     * Confirmar un pedido online y crear venta
     */
    public function confirmOrder(int $orderId, int $userId): Sale
    {
        return DB::transaction(function () use ($orderId, $userId) {
            $order = Order::with('details')->findOrFail($orderId);

            if ($order->status !== 'pending') {
                throw new \Exception('El pedido ya fue procesado');
            }

            // Validar stock
            $items = $order->details->map(function ($detail) {
                return [
                    'product_variant_id' => $detail->product_variant_id,
                    'quantity' => $detail->quantity,
                ];
            })->toArray();

            $this->validateStock($items);

            // Crear venta
            $sale = Sale::create([
                'user_id' => $userId,
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'sale_number' => Sale::generateSaleNumber(),
                'sale_type' => 'online',
                'payment_method' => null, // Se define después
                'subtotal' => $order->subtotal,
                'discount' => 0,
                'total' => $order->total,
            ]);

            // Crear detalles y reducir stock
            foreach ($order->details as $detail) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_variant_id' => $detail->product_variant_id,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'subtotal' => $detail->subtotal,
                ]);

                // Reducir stock
                $this->inventoryService->reduceStock(
                    $detail->product_variant_id,
                    $detail->quantity,
                    $userId,
                    $sale->sale_number
                );
            }

            // Actualizar estado del pedido
            $order->update(['status' => 'confirmed']);

            return $sale->load(['details.productVariant', 'customer', 'order']);
        });
    }

    /**
     * Cancelar un pedido
     */
    public function cancelOrder(int $orderId, ?string $reason = null): Order
    {
        $order = Order::findOrFail($orderId);

        if (!$order->canBeCancelled()) {
            throw new \Exception('Este pedido no puede ser cancelado');
        }

        $order->update([
            'status' => 'cancelled',
            'notes' => ($order->notes ?? '') . "\nMotivo de cancelación: " . ($reason ?? 'No especificado'),
        ]);

        return $order;
    }

    /**
     * Obtener ventas con filtros
     */
    public function getSales(array $filters = [])
    {
        $query = Sale::with(['details.productVariant.product', 'customer', 'user']);

        // Filtro por tipo
        if (isset($filters['sale_type'])) {
            $query->where('sale_type', $filters['sale_type']);
        }

        // Filtro por fecha
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filtro por vendedor
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Obtener detalle de una venta
     */
    public function getSaleDetails(int $saleId): Sale
    {
        return Sale::with([
            'details.productVariant.product',
            'details.productVariant.size',
            'details.productVariant.color',
            'customer',
            'user',
            'order',
        ])->findOrFail($saleId);
    }

    /**
     * Validar disponibilidad de stock para múltiples items
     */
    private function validateStock(array $items): void
    {
        foreach ($items as $item) {
            if (!$this->inventoryService->checkStockAvailability(
                $item['product_variant_id'],
                $item['quantity']
            )) {
                throw new \Exception('Stock insuficiente para uno o más productos');
            }
        }
    }

    /**
     * Calcular subtotal de items
     */
    private function calculateSubtotal(array $items): float
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += $item['unit_price'] * $item['quantity'];
        }

        return $subtotal;
    }

    /**
     * Agregar detalle a una venta
     */
    private function addSaleDetail(int $saleId, array $item): SaleDetail
    {
        return SaleDetail::create([
            'sale_id' => $saleId,
            'product_variant_id' => $item['product_variant_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'subtotal' => $item['unit_price'] * $item['quantity'],
        ]);
    }

    /**
     * Buscar o crear cliente
     */
    private function findOrCreateCustomer(array $customerData): Customer
    {
        // Buscar por email o teléfono
        $customer = null;

        if (isset($customerData['email'])) {
            $customer = Customer::where('email', $customerData['email'])->first();
        }

        if (!$customer && isset($customerData['phone'])) {
            $customer = Customer::where('phone', $customerData['phone'])->first();
        }

        // Crear si no existe
        if (!$customer) {
            $customer = Customer::create([
                'name' => $customerData['name'],
                'email' => $customerData['email'] ?? null,
                'phone' => $customerData['phone'] ?? null,
                'address' => $customerData['address'] ?? null,
                'city' => $customerData['city'] ?? null,
            ]);
        }

        return $customer;
    }
}