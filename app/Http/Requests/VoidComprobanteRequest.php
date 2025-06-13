<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoidComprobanteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules()
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'motivo' => 'required|string|max:100',
        ];
    }
}