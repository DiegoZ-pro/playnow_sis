<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:cash,card,transfer',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            
            // Datos del cliente (opcionales)
            'customer.name' => 'nullable|string|max:100',
            'customer.email' => 'nullable|email|max:100',
            'customer.phone' => 'nullable|string|max:20',
            'customer.address' => 'nullable|string',
            'customer.city' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_method.required' => 'El método de pago es obligatorio',
            'payment_method.in' => 'El método de pago no es válido',
            'items.required' => 'Debe agregar al menos un producto',
            'items.min' => 'Debe agregar al menos un producto',
            'items.*.product_variant_id.required' => 'El producto es obligatorio',
            'items.*.product_variant_id.exists' => 'El producto seleccionado no existe',
            'items.*.quantity.required' => 'La cantidad es obligatoria',
            'items.*.quantity.min' => 'La cantidad debe ser al menos 1',
            'items.*.unit_price.required' => 'El precio es obligatorio',
            'customer.email.email' => 'El email no es válido',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Asegurar que user_id sea el del usuario autenticado
        if (Auth::check()) {
            $this->merge([
                'user_id' => Auth::id(),
            ]);
        }
    }
}