<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Los pedidos desde la tienda no requieren autenticación
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Datos del cliente
            'customer.name' => 'required|string|max:100',
            'customer.email' => 'nullable|email|max:100',
            'customer.phone' => 'required|string|max:20',
            'customer.address' => 'nullable|string',
            'customer.city' => 'nullable|string|max:100',
            
            // Dirección de envío
            'shipping_address' => 'required|string',
            
            // Notas adicionales
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer.name.required' => 'El nombre es obligatorio',
            'customer.name.max' => 'El nombre no puede exceder 100 caracteres',
            'customer.email.email' => 'El email no es válido',
            'customer.phone.required' => 'El teléfono es obligatorio',
            'customer.phone.max' => 'El teléfono no puede exceder 20 caracteres',
            'shipping_address.required' => 'La dirección de envío es obligatoria',
            'notes.max' => 'Las notas no pueden exceder 500 caracteres',
        ];
    }
}