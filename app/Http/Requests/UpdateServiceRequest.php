<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'costo' => 'sometimes|numeric|min:0',
            'fecha_inicio' => 'sometimes|date',
            'estado' => 'sometimes|in:activo,inactivo,pendiente',
        ];
    }
}
