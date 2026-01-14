@extends('sistema.layouts.app')
@section('title', 'Usuarios')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between">
        <h3 class="text-lg font-semibold">Gestión de Usuarios</h3>
        <a href="{{ route('sistema.users.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg">Nuevo Usuario</a>
    </div>
    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-sm">{{ $user->role->name }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $user->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $user->active ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('sistema.users.edit', $user->id) }}" class="text-blue-600"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection