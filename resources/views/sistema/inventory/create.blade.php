@extends('sistema.layouts.app')

@section('title', 'Crear Producto')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-6">Crear Nuevo Producto</h3>
        <p class="text-sm text-gray-600 mb-6">Complete los datos del producto</p>

        {{-- ⭐ AGREGADO: enctype="multipart/form-data" --}}
        <form method="POST" action="{{ route('sistema.inventory.store') }}" id="productForm" enctype="multipart/form-data">
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
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300">
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

                <!-- Sección de Imágenes -->
                <div class="border-t pt-6">
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-images mr-2 text-red-600"></i>
                            Imágenes del Producto
                        </h4>
                        <p class="text-sm text-gray-600">
                            Puedes subir hasta 5 imágenes. La primera será la imagen principal.
                        </p>
                        <p class="text-xs text-gray-500">
                            Formatos: JPG, PNG, WEBP | Tamaño máximo: 5MB por imagen
                        </p>
                    </div>

                    {{-- ⭐ INPUT REAL CON name="images[]" --}}
                    <input type="file" 
                           name="images[]" 
                           id="imageInput" 
                           multiple 
                           accept="image/jpeg,image/jpg,image/png,image/webp"
                           class="hidden">

                    <!-- Área de drop -->
                    <div id="dropZone"
                         class="border-2 border-dashed border-gray-300 rounded-lg p-10 text-center cursor-pointer transition hover:border-red-500 hover:bg-gray-50">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="font-medium text-gray-700">
                            Arrastra imágenes aquí o haz clic para seleccionar
                        </p>
                        <p class="text-sm text-gray-500">
                            Máximo 5 imágenes (JPG, PNG, WEBP - hasta 5MB c/u)
                        </p>
                    </div>

                    <!-- Preview de imágenes -->
                    <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4 hidden">
                        <!-- Las previews se agregarán aquí con JavaScript -->
                    </div>

                    <!-- Contador -->
                    <p id="imageCounter" class="text-sm text-gray-600 mt-2 hidden">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span id="imageCount">0</span> de 5 imágenes agregadas
                    </p>
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
// ============================================
// VARIANTES
// ============================================
let variantIndex = 0;

// Datos pasados desde PHP
const sizes = @json($sizes);
const colors = @json($colors);

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

// ============================================
// IMÁGENES (SIN ALPINE.JS)
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('imageInput');
    const dropZone = document.getElementById('dropZone');
    const imagePreview = document.getElementById('imagePreview');
    const imageCounter = document.getElementById('imageCounter');
    const imageCount = document.getElementById('imageCount');
    
    let selectedFiles = [];
    const maxFiles = 5;
    const maxSize = 5 * 1024 * 1024; // 5MB

    // Click en dropzone abre selector
    dropZone.addEventListener('click', () => imageInput.click());

    // Drag & Drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-red-500', 'bg-red-50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-red-500', 'bg-red-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-red-500', 'bg-red-50');
        handleFiles(e.dataTransfer.files);
    });

    // Cambio en input file
    imageInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        const fileArray = Array.from(files);
        
        fileArray.forEach(file => {
            // Validar cantidad
            if (selectedFiles.length >= maxFiles) {
                alert('Máximo 5 imágenes permitidas');
                return;
            }

            // Validar tamaño
            if (file.size > maxSize) {
                alert(`${file.name} excede el tamaño máximo de 5MB`);
                return;
            }

            // Validar tipo
            if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
                alert(`${file.name} no es un formato válido`);
                return;
            }

            selectedFiles.push(file);
        });

        updatePreview();
        updateInput();
    }

    function updatePreview() {
        if (selectedFiles.length === 0) {
            imagePreview.classList.add('hidden');
            imageCounter.classList.add('hidden');
            return;
        }

        imagePreview.classList.remove('hidden');
        imageCounter.classList.remove('hidden');
        imageCount.textContent = selectedFiles.length;
        imagePreview.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <img src="${e.target.result}" 
                         class="w-full h-28 object-cover rounded-lg border-2 ${index === 0 ? 'border-red-500' : 'border-gray-200'}">
                    ${index === 0 ? '<span class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded"><i class="fas fa-star"></i> Principal</span>' : ''}
                    <span class="absolute top-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">#${index + 1}</span>
                    <button type="button" onclick="removeImage(${index})"
                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                        <span class="bg-red-600 text-white px-3 py-2 rounded">
                            <i class="fas fa-trash"></i>
                        </span>
                    </button>
                    <p class="text-xs text-gray-600 mt-1 truncate">${file.name}</p>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    function updateInput() {
        // Crear nuevo DataTransfer con los archivos seleccionados
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        imageInput.files = dataTransfer.files;
    }

    // Función global para eliminar imagen
    window.removeImage = function(index) {
        selectedFiles.splice(index, 1);
        updatePreview();
        updateInput();
    };
});
</script>
@endpush