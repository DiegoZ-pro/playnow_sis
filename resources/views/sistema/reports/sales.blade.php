@extends('sistema.layouts.app')
@section('title', 'Reporte de Ventas')
@section('content')
<div class="space-y-6">
    <h3 class="text-lg font-semibold">Reporte de Ventas</h3>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="border rounded-lg p-4">
                <p class="text-gray-600 text-sm">Total Ventas</p>
                <p class="text-2xl font-bold">{{ $report['summary']['total_sales'] }}</p>
            </div>
            <div class="border rounded-lg p-4">
                <p class="text-gray-600 text-sm">Ingresos Totales</p>
                <p class="text-2xl font-bold text-green-600">Bs {{ number_format($report['summary']['total_revenue'], 2) }}</p>
            </div>
            <div class="border rounded-lg p-4">
                <p class="text-gray-600 text-sm">Venta Promedio</p>
                <p class="text-2xl font-bold">Bs {{ number_format($report['summary']['average_sale'], 2) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection