<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Permitir solicitudes
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'costo' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'estado' => 'required|in:activo,inactivo,pendiente',
        ];
    }
}
