@extends('sistema.layouts.app')
@section('title', 'Reporte de Inventario')
@section('content')
<div class="space-y-6">
    <h3 class="text-lg font-semibold">Reporte de Inventario</h3>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="border rounded-lg p-4">
                <p class="text-gray-600 text-sm">Total Productos</p>
                <p class="text-2xl font-bold">{{ $report['summary']['total_products'] }}</p>
            </div>
            <div class="border rounded-lg p-4">
                <p class="text-gray-600 text-sm">Total Variantes</p>
                <p class="text-2xl font-bold">{{ $report['summary']['total_variants'] }}</p>
            </div>
            <div class="border rounded-lg p-4">
                <p class="text-gray-600 text-sm">Stock Total</p>
                <p class="text-2xl font-bold">{{ $report['summary']['total_stock'] }}</p>
            </div>
            <div class="border rounded-lg p-4">
                <p class="text-gray-600 text-sm">Stock Bajo</p>
                <p class="text-2xl font-bold text-red-600">{{ $report['summary']['low_stock_items'] }}</p>
            </div>
        </div>
    </div>
</div>
@endsection