@extends('sistema.layouts.app')
@section('title', 'Nuevo Cliente')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-6">Registrar Nuevo Cliente</h3>
        <form method="POST" action="{{ route('sistema.customers.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="phone" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="address" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" name="city" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="flex gap-2 pt-4">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">Guardar</button>
                    <a href="{{ route('sistema.customers.index') }}" class="bg-gray-200 px-6 py-2 rounded-lg">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection