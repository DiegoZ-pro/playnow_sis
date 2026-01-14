@extends('sistema.layouts.app')

@section('title', 'Crear Producto')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-6">Crear Nuevo Producto</h3>
        <p class="text-sm text-gray-600 mb-6">Complete los datos del producto</p>

        <form method="POST" action="{{ route('sistema.inventory.store') }}" id="productForm">
            @csrf

            <div class="space-y-4">
                <!-- Nombre y Categoría -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" name="name" required value="{{ old('name') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                        <select name="category_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                            <option value="">Seleccione...</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Marca y Precio -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marca *</label>
                        <select name="brand_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                            <option value="">Seleccione...</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio Base *</label>
                        <input type="number" name="base_price" required step="0.01" value="{{ old('base_price') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">{{ old('description') }}</textarea>
                </div>

                <!-- Opciones -->
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="featured" value="1" class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Producto Destacado</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="active" value="1" checked class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Activo</span>
                    </label>
                </div>

                <!-- SECCIÓN DE VARIANTES -->
                <div class="border-t pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">Variantes del Producto</h4>
                            <p class="text-sm text-gray-600">Agrega combinaciones de talla y color</p>
                        </div>
                        <button type="button" 
                                onclick="addVariantRow()" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                            <i class="fas fa-plus mr-2"></i>Agregar Variante
                        </button>
                    </div>

                    <div id="variantsList" class="space-y-3">
                        <!-- Las variantes se agregan aquí dinámicamente -->
                    </div>

                    <div id="emptyMessage" class="text-center py-8 text-gray-500 border-2 border-dashed rounded-lg">
                        <i class="fas fa-box-open text-3xl mb-2"></i>
                        <p class="text-sm">No hay variantes agregadas</p>
                        <p class="text-xs">Click en "Agregar Variante" para crear combinaciones de talla/color</p>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-2 pt-4 border-t">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                        <i class="fas fa-save mr-2"></i>Guardar Producto
                    </button>
                    <a href="{{ route('sistema.inventory.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let variantIndex = 0;

// Datos pasados desde PHP
const sizes = JSON.parse('<?php echo json_encode($sizes); ?>');
const colors = JSON.parse('<?php echo json_encode($colors); ?>');

function addVariantRow() {
    const container = document.getElementById('variantsList');
    const emptyMessage = document.getElementById('emptyMessage');
    
    const variantHtml = `
        <div class="border rounded-lg p-4 bg-gray-50" id="variant-${variantIndex}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Talla *</label>
                    <select name="variants[${variantIndex}][size_id]" required class="w-full border rounded px-2 py-2 text-sm">
                        <option value="">Seleccione...</option>
                        ${sizes.map(size => `<option value="${size.id}">${size.value}</option>`).join('')}
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Color *</label>
                    <select name="variants[${variantIndex}][color_id]" required class="w-full border rounded px-2 py-2 text-sm">
                        <option value="">Seleccione...</option>
                        ${colors.map(color => `<option value="${color.id}">${color.name}</option>`).join('')}
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Stock *</label>
                    <input type="number" name="variants[${variantIndex}][stock]" min="0" required class="w-full border rounded px-2 py-2 text-sm" value="0">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Precio</label>
                    <input type="number" name="variants[${variantIndex}][price]" step="0.01" placeholder="Usar precio base" class="w-full border rounded px-2 py-2 text-sm">
                </div>

                <div class="flex items-end">
                    <button type="button" onclick="removeVariant(${variantIndex})" class="w-full bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', variantHtml);
    emptyMessage.style.display = 'none';
    variantIndex++;
}

function removeVariant(index) {
    const variant = document.getElementById(`variant-${index}`);
    variant.remove();
    
    const container = document.getElementById('variantsList');
    const emptyMessage = document.getElementById('emptyMessage');
    
    if (container.children.length === 0) {
        emptyMessage.style.display = 'block';
    }
}
</script>
@endpush