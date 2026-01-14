@extends('sistema.layouts.app')

@section('title', 'Inventario')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Gestión de Inventario</h3>
            <p class="text-sm text-gray-600">Administra los productos y su stock</p>
        </div>
        <a href="{{ route('sistema.inventory.create') }}" 
           class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
            <i class="fas fa-plus mr-2"></i>
            Nuevo Producto
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('sistema.inventory.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Búsqueda -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Nombre del producto..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
            </div>

            <!-- Categoría -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    <option value="">Todas</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Marca -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                <select name="brand_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    <option value="">Todas</option>
                    @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="active" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    <option value="">Todos</option>
                    <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <!-- Botones -->
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
                <a href="{{ route('sistema.inventory.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-times mr-2"></i>Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marca</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Base</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variantes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($product->images->first())
                                    <img class="h-10 w-10 rounded object-cover" 
                                         src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                         alt="{{ $product->name }}">
                                    @else
                                    <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $product->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->category->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->brand->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            Bs. {{ number_format($product->base_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->variants->count() }} variantes
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Activo
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactivo
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <a href="{{ route('sistema.inventory.show', $product->id) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sistema.inventory.edit', $product->id) }}" 
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('sistema.inventory.destroy', $product->id) }}"
                                      onsubmit="return confirm('¿Estás seguro de desactivar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-3 text-gray-400"></i>
                            <p>No se encontraron productos</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($products->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection