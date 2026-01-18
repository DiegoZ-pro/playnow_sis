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
                            <div class="flex items-center gap-3">
                                {{-- IMAGEN --}}
                                <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded overflow-hidden">
                                    @php
                                        $primaryImage = $product->images->where('is_primary', true)->first() 
                                                        ?? $product->images->first();
                                    @endphp

                                    @if($primaryImage)
                                        <img src="{{ asset('storage/' . $primaryImage->image_url) }}" 
                                            alt="{{ $product->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                            <i class="fas fa-image text-gray-400 text-xl"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- NOMBRE Y SLUG --}}
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $product->slug }}</p>
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
                                   class="text-blue-600 hover:text-blue-900"
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sistema.inventory.edit', $product->id) }}" 
                                   class="text-green-600 hover:text-green-900"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('sistema.inventory.destroy', $product->id) }}"
                                      class="inline"
                                      id="delete-form-{{ $product->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            onclick="confirmDelete({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                            class="text-red-600 hover:text-red-900"
                                            title="Eliminar">
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

<!-- Modal de Confirmación (Bootstrap/Tailwind Style) -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">¿Eliminar producto?</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    ¿Estás seguro de eliminar <strong id="productName"></strong>?
                </p>
                <p class="text-xs text-gray-400 mt-2">Esta acción no se puede deshacer</p>
            </div>
            <div class="flex gap-3 px-4 py-3">
                <button onclick="closeModal()" 
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancelar
                </button>
                <button onclick="submitDelete()" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteFormId = null;

function confirmDelete(productId, productName) {
    deleteFormId = productId;
    document.getElementById('productName').textContent = productName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteFormId = null;
}

function submitDelete() {
    if (deleteFormId) {
        document.getElementById('delete-form-' + deleteFormId).submit();
    }
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Cerrar modal al hacer clic fuera
document.getElementById('deleteModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeModal();
    }
});
</script>
@endpush