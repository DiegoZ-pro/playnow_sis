@extends('sistema.layouts.app')

@section('title', 'Detalle de Venta')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Venta {{ $sale->sale_number }}</h3>
            <p class="text-sm text-gray-600">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('sistema.sales.print', $sale->id) }}" target="_blank"
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-print mr-2"></i>Imprimir
            </a>
            <a href="{{ route('sistema.sales.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detalles de la Venta -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Productos -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h4 class="font-semibold text-gray-900">Productos</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($sale->details as $detail)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $detail->productVariant->product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $detail->productVariant->product->brand->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $detail->productVariant->size->value }} / {{ $detail->productVariant->color->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $detail->quantity }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">Bs {{ number_format($detail->unit_price, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    Bs {{ number_format($detail->unit_price * $detail->quantity, 2, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Información de Pago y Cliente -->
        <div class="space-y-6">
            <!-- Resumen de Pago -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="font-semibold text-gray-900 mb-4">Resumen</h4>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Subtotal:</dt>
                        <dd class="font-semibold">Bs {{ number_format($sale->subtotal, 2, ',', '.') }}</dd>
                    </div>
                    @if($sale->discount > 0)
                    <div class="flex justify-between text-red-600">
                        <dt>Descuento:</dt>
                        <dd class="font-semibold">- Bs {{ number_format($sale->discount, 2, ',', '.') }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t">
                        <dt class="font-bold text-lg">TOTAL:</dt>
                        <dd class="font-bold text-lg text-red-600">Bs {{ number_format($sale->total, 2, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Método de Pago -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="font-semibold text-gray-900 mb-4">Pago</h4>
                <p class="text-sm text-gray-600">Método de pago:</p>
                <p class="font-semibold">
                    @if($sale->payment_method == 'cash')
                        <i class="fas fa-money-bill-wave mr-2"></i>Efectivo
                    @elseif($sale->payment_method == 'card')
                        <i class="fas fa-credit-card mr-2"></i>Tarjeta
                    @else
                        <i class="fas fa-exchange-alt mr-2"></i>Transferencia
                    @endif
                </p>
            </div>

            <!-- Cliente -->
            @if($sale->customer)
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="font-semibold text-gray-900 mb-4">Cliente</h4>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-gray-600">Nombre:</dt>
                        <dd class="font-medium">{{ $sale->customer->name }}</dd>
                    </div>
                    @if($sale->customer->phone)
                    <div>
                        <dt class="text-gray-600">Teléfono:</dt>
                        <dd>{{ $sale->customer->phone }}</dd>
                    </div>
                    @endif
                    @if($sale->customer->email)
                    <div>
                        <dt class="text-gray-600">Email:</dt>
                        <dd>{{ $sale->customer->email }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            <!-- Vendedor -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="font-semibold text-gray-900 mb-4">Vendedor</h4>
                <p class="text-sm">{{ $sale->user->name }}</p>
                <p class="text-xs text-gray-600">{{ $sale->user->role->name }}</p>
            </div>
        </div>
    </div>
</div>
@endsection