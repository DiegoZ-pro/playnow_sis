@extends('sistema.layouts.app')
@section('title', 'Nuevo Usuario')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold mb-6">Crear Usuario</h3>
        <form method="POST" action="{{ route('sistema.users.store') }}">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-medium mb-1">Nombre *</label><input type="text" name="name" required class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium mb-1">Email *</label><input type="email" name="email" required class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium mb-1">Contraseña *</label><input type="password" name="password" required class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium mb-1">Confirmar Contraseña *</label><input type="password" name="password_confirmation" required class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium mb-1">Rol *</label><select name="role_id" required class="w-full border rounded-lg px-3 py-2">@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select></div>
                <div><label class="flex items-center"><input type="checkbox" name="active" value="1" checked class="rounded"> <span class="ml-2">Usuario Activo</span></label></div>
                <div class="flex gap-2 pt-4"><button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg">Guardar</button><a href="{{ route('sistema.users.index') }}" class="bg-gray-200 px-6 py-2 rounded-lg">Cancelar</a></div>
            </div>
        </form>
    </div>
</div>
@endsection