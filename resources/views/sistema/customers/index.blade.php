@extends('sistema.layouts.app')
@section('title', 'Clientes')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Gestión de Clientes</h3>
        <a href="{{ route('sistema.customers.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
            <i class="fas fa-plus mr-2"></i>Nuevo Cliente
        </a>
    </div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compras</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $customer->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <div>{{ $customer->phone ?? '-' }}</div>
                        <div class="text-xs">{{ $customer->email ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $customer->sales_count }} ventas</td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('sistema.customers.show', $customer->id) }}" class="text-blue-600 hover:text-blue-900 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('sistema.customers.edit', $customer->id) }}" class="text-green-600 hover:text-green-900"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No hay clientes</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($customers->hasPages())<div class="px-4 py-3 border-t">{{ $customers->links() }}</div>@endif
    </div>
</div>
@endsection