<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Listar clientes
     */
    public function index(Request $request)
    {
        $query = Customer::withCount('sales');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(20);

        return view('sistema.customers.index', compact('customers'));
    }

    /**
     * Mostrar formulario de crear cliente
     */
    public function create()
    {
        return view('sistema.customers.create');
    }

    /**
     * Guardar nuevo cliente
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
        ]);

        try {
            $customer = Customer::create($request->all());

            return redirect()
                ->route('sistema.customers.show', $customer->id)
                ->with('success', 'Cliente registrado exitosamente');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al registrar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de cliente
     */
    public function show($id)
    {
        $customer = Customer::with(['sales.details.productVariant.product'])
            ->withCount('sales')
            ->findOrFail($id);

        // Calcular estadísticas
        $totalSpent = $customer->sales->sum('total');
        $averagePurchase = $customer->sales_count > 0 
            ? $totalSpent / $customer->sales_count 
            : 0;

        return view('sistema.customers.show', compact('customer', 'totalSpent', 'averagePurchase'));
    }

    /**
     * Mostrar formulario de editar cliente
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        return view('sistema.customers.edit', compact('customer'));
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
        ]);

        try {
            $customer->update($request->all());

            return redirect()
                ->route('sistema.customers.show', $customer->id)
                ->with('success', 'Cliente actualizado exitosamente');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar cliente
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            // Verificar si tiene ventas
            if ($customer->sales()->count() > 0) {
                return back()->with('error', 'No se puede eliminar un cliente con ventas registradas');
            }

            $customer->delete();

            return redirect()
                ->route('sistema.customers.index')
                ->with('success', 'Cliente eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}