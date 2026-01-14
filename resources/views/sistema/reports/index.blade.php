@extends('sistema.layouts.app')
@section('title', 'Reportes')
@section('content')
<div class="space-y-6">
    <h3 class="text-lg font-semibold">Centro de Reportes</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('sistema.reports.sales') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-4"><i class="fas fa-chart-line text-green-600 text-2xl"></i></div>
                <div class="ml-4">
                    <h4 class="font-semibold text-gray-900">Reporte de Ventas</h4>
                    <p class="text-sm text-gray-600">Análisis de ventas por período</p>
                </div>
            </div>
        </a>
        <a href="{{ route('sistema.reports.inventory') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-4"><i class="fas fa-boxes text-blue-600 text-2xl"></i></div>
                <div class="ml-4">
                    <h4 class="font-semibold text-gray-900">Reporte de Inventario</h4>
                    <p class="text-sm text-gray-600">Estado del inventario</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection