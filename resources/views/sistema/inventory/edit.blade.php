@extends('sistema.layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Editar Producto</h3>
            <p class="text-sm text-gray-600">{{ $product->name }}</p>
        </div>

        <form method="POST" action="{{ route('sistema.inventory.update', $product->id) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                        <select name="category_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marca *</label>
                        <select name="brand_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio Base (Bs) *</label>
                        <input type="number" name="base_price" value="{{ old('base_price', $product->base_price) }}" step="0.01" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="featured" value="1" {{ $product->featured ? 'checked' : '' }} class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Producto Destacado</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="active" value="1" {{ $product->active ? 'checked' : '' }} class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Activo</span>
                    </label>
                </div>

                <div class="flex gap-2 pt-4 border-t">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('sistema.inventory.show', $product->id) }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection