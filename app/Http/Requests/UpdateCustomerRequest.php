<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
            'name' => 'required|max:255|min:3',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($this->route('customer')->id),
            ],
            'phone' => [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('customers', 'phone')->ignore($this->route('customer')->id),
            ],
            'address' => [
                'required',
                'min:10',
                'max:255'
            ],
            'city' => 'required',
            'state' => 'required',
            'postal_code' => 'required|numeric|digits:6'
        ];
    }
}
