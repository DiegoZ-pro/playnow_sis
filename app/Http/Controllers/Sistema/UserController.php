<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Listar usuarios
     */
    public function index()
    {
        $users = User::with('role')->latest()->paginate(20);

        return view('sistema.users.index', compact('users'));
    }

    /**
     * Mostrar formulario de crear usuario
     */
    public function create()
    {
        $roles = Role::all();

        return view('sistema.users.create', compact('roles'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'active' => 'boolean',
        ], [
            'role_id.required' => 'El rol es obligatorio',
            'role_id.exists' => 'El rol seleccionado no existe',
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        try {
            $user = User::create([
                'role_id' => $request->role_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'active' => $request->boolean('active', true),
            ]);

            return redirect()
                ->route('sistema.users.index')
                ->with('success', 'Usuario creado exitosamente');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de editar usuario
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('sistema.users.edit', compact('user', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'active' => 'boolean',
        ], [
            'role_id.required' => 'El rol es obligatorio',
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        try {
            $data = [
                'role_id' => $request->role_id,
                'name' => $request->name,
                'email' => $request->email,
                'active' => $request->boolean('active', true),
            ];

            // Solo actualizar contraseña si se proporciona
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()
                ->route('sistema.users.index')
                ->with('success', 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // No permitir eliminar el propio usuario
            if ($user->id === Auth::id()) {
                return back()->with('error', 'No puedes eliminar tu propio usuario');
            }

            // Verificar si tiene ventas asociadas
            if ($user->sales()->count() > 0) {
                return back()->with('error', 'No se puede eliminar un usuario con ventas registradas');
            }

            $user->delete();

            return redirect()
                ->route('sistema.users.index')
                ->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}