@extends('sistema.layouts.app')

@section('title', 'Pedidos Online')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Pedidos de la Tienda Online</h3>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pedido</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium text-gray-900">{{ $order->customer->name }}</div>
                            <div class="text-gray-500">{{ $order->customer->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            Bs {{ number_format($order->total, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order->status == 'pending')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pendiente
                            </span>
                            @elseif($order->status == 'confirmed')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Confirmado
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Cancelado
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($order->status == 'pending')
                            <button onclick="confirmOrder({{ $order->id }})" class="text-green-600 hover:text-green-900 mr-2">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button onclick="cancelOrder({{ $order->id }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No hay pedidos pendientes
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="bg-white px-4 py-3 border-t">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmOrder(orderId) {
    if (!confirm('¿Confirmar este pedido y crear la venta?')) return;

    fetch(`/sistema/ventas/pedidos/${orderId}/confirmar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert(data.message);
        }
    });
}

function cancelOrder(orderId) {
    const reason = prompt('Motivo de cancelación:');
    if (!reason) return;

    fetch(`/sistema/ventas/pedidos/${orderId}/cancelar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>
@endpush