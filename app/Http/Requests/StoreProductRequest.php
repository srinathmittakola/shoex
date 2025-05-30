<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'shoe_for' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'actual_price' => 'required|numeric|min:0|lt:mrp',
            'stock' => 'required|integer|min:0',
            'image_paths' => 'required|array|min:1',
            'image_paths.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:2048',
        ];
    }
}
