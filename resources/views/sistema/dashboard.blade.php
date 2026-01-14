@extends('sistema.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Ventas Hoy -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ventas Hoy</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $metrics['sales_today']['count'] }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        Bs {{ number_format($metrics['sales_today']['total'], 2, ',', '.') }}
                    </p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Ventas Este Mes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ventas Este Mes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $metrics['sales_this_month']['count'] }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        Bs {{ number_format($metrics['sales_this_month']['total'], 2, ',', '.') }}
                    </p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-calendar-alt text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Ventas Este Año -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ventas Este Año</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $metrics['sales_this_year']['count'] }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        Bs {{ number_format($metrics['sales_this_year']['total'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Productos Stock Bajo</p>
                    <p class="text-2xl font-bold text-red-600">{{ $metrics['low_stock_products'] }}</p>
                    <a href="{{ route('sistema.inventory.index') }}?low_stock=1" class="text-sm text-blue-600 hover:underline mt-1 inline-block">
                        Ver productos
                    </a>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pedidos Pendientes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pedidos Pendientes</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $metrics['pending_orders'] }}</p>
                    <a href="{{ route('sistema.sales.orders') }}" class="text-sm text-blue-600 hover:underline mt-1 inline-block">
                        Ver pedidos
                    </a>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-shopping-cart text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Clientes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Clientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $metrics['total_customers'] }}</p>
                    <a href="{{ route('sistema.customers.index') }}" class="text-sm text-blue-600 hover:underline mt-1 inline-block">
                        Ver clientes
                    </a>
                </div>
                <div class="bg-indigo-100 rounded-full p-4">
                    <i class="fas fa-users text-indigo-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Tablas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de Ventas -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ventas Últimos 7 Días</h3>
            <div style="height: 300px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Top Productos -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Productos Más Vendidos</h3>
            <div class="space-y-3">
                @forelse($topProducts as $product)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $product->name }}</p>
                        <p class="text-sm text-gray-600">{{ $product->category_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">{{ $product->total_sold }} uds</p>
                        <p class="text-sm text-green-600">Bs {{ number_format($product->total_revenue, 0, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Productos con Stock Bajo -->
    @if($lowStockProducts->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Productos con Stock Bajo</h3>
            <a href="{{ route('sistema.inventory.index') }}" class="text-sm text-blue-600 hover:underline">
                Ver todos
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($lowStockProducts as $variant)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $variant->product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $variant->product->brand->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $variant->sku }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $variant->size->value }} / {{ $variant->color->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $variant->stock }} unidades
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('sistema.inventory.show', $variant->product_id) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                Ajustar Stock
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Ventas
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: JSON.parse('<?php echo json_encode($salesChart["labels"]); ?>'),
            datasets: [{
                label: 'Cantidad de Ventas',
                data: JSON.parse('<?php echo json_encode($salesChart["counts"]); ?>'),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush