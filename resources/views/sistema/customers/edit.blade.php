@extends('sistema.layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Editar Cliente</h3>
            <p class="text-sm text-gray-600">{{ $customer->name }}</p>
        </div>

        <form method="POST" action="{{ route('sistema.customers.update', $customer->id) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $customer->name) }}" 
                           required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" 
                               name="phone" 
                               value="{{ old('phone', $customer->phone) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $customer->email) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" 
                           name="address" 
                           value="{{ old('address', $customer->address) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" 
                           name="city" 
                           value="{{ old('city', $customer->city) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                </div>

                <div class="flex gap-2 pt-4 border-t">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('sistema.customers.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection