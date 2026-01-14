@extends('sistema.layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Editar Usuario</h3>
            <p class="text-sm text-gray-600">{{ $user->email }}</p>
        </div>

        <form method="POST" action="{{ route('sistema.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $user->name) }}" 
                           required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}" 
                           required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" 
                           name="password" 
                           placeholder="Dejar en blanco para mantener la actual" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    <p class="text-xs text-gray-500 mt-1">Solo completa si deseas cambiar la contraseña</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                    <input type="password" 
                           name="password_confirmation" 
                           placeholder="Confirmar nueva contraseña" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
                    <select name="role_id" 
                            required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="active" 
                               value="1" 
                               {{ $user->active ? 'checked' : '' }} 
                               class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Usuario Activo</span>
                    </label>
                </div>

                <div class="flex gap-2 pt-4 border-t">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('sistema.users.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection