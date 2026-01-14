@extends('sistema.layouts.app')
@section('title', 'Detalle Cliente')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between">
        <h3 class="text-xl font-semibold">{{ $customer->name }}</h3>
        <div class="flex gap-2">
            <a href="{{ route('sistema.customers.edit', $customer->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Editar</a>
            <a href="{{ route('sistema.customers.index') }}" class="bg-gray-200 px-4 py-2 rounded-lg">Volver</a>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold mb-4">Información</h4>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-gray-600">Teléfono:</dt><dd>{{ $customer->phone ?? '-' }}</dd></div>
                <div><dt class="text-gray-600">Email:</dt><dd>{{ $customer->email ?? '-' }}</dd></div>
                <div><dt class="text-gray-600">Dirección:</dt><dd>{{ $customer->address ?? '-' }}</dd></div>
                <div><dt class="text-gray-600">Ciudad:</dt><dd>{{ $customer->city ?? '-' }}</dd></div>
            </dl>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold mb-4">Estadísticas</h4>
            <dl class="space-y-2">
                <div><dt class="text-gray-600">Total Compras:</dt><dd class="text-2xl font-bold">{{ $customer->sales_count }}</dd></div>
                <div><dt class="text-gray-600">Total Gastado:</dt><dd class="text-lg font-semibold text-green-600">Bs {{ number_format($totalSpent, 2) }}</dd></div>
            </dl>
        </div>
    </div>
</div>
@endsection