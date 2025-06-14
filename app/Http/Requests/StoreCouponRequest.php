<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'codename' => 'required',
            'code' => 'required|unique:coupons,code',
            'discount' => 'required|numeric|max:100|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ];
    }
}