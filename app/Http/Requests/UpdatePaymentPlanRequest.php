<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentPlanRequest extends FormRequest
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
            'service_id'    => 'required|exists:services,id',
            'customer_id'     => 'required|exists:customers,id',
            'period_id'     => 'required|exists:periods,id',
            'payment_type'  => 'required|string|in:anual,mensual',
            'amount'        => 'required|numeric|min:0|max:9999.99',
            'duration'      => 'required|integer|min:1|max:120',
            'state' => 'required|boolean',
        ];
    }
}
