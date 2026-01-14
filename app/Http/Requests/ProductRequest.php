<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        return $user->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'featured' => 'boolean',
            'active' => 'boolean',
        ];

        // Validaciones para variantes si se incluyen
        if ($this->has('variants')) {
            $rules['variants'] = 'array';
            $rules['variants.*.size_id'] = 'required|exists:sizes,id';
            $rules['variants.*.color_id'] = 'required|exists:colors,id';
            $rules['variants.*.price'] = 'nullable|numeric|min:0';
            $rules['variants.*.stock'] = 'nullable|integer|min:0';
            $rules['variants.*.low_stock_threshold'] = 'nullable|integer|min:0';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'La categoría es obligatoria',
            'category_id.exists' => 'La categoría seleccionada no existe',
            'brand_id.required' => 'La marca es obligatoria',
            'brand_id.exists' => 'La marca seleccionada no existe',
            'name.required' => 'El nombre del producto es obligatorio',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'base_price.required' => 'El precio base es obligatorio',
            'base_price.numeric' => 'El precio debe ser un número',
            'base_price.min' => 'El precio debe ser mayor o igual a 0',
        ];
    }
}