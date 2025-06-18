<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:100|unique:customers,email',
            'dni' => 'nullable|string|max:8|unique:customers,dni',
            'ruc' => 'nullable|string|max:11|unique:customers,ruc',
            'codigo' => 'required|string|max:11|unique:customers,codigo',
            'client_type_id' => 'required|exists:client_types,id',
        ];
    }
}
