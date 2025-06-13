<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'payment_id' => $this->payment_id,
            'document_type' => $this->document_type === 'B' ? 'Boleta' : 'Factura',
            'serie_assigned' => $this->serie_assigned,
            'correlative_assigned' => $this->correlative_assigned,
            'sunat' => $this->sunat ?? 'Pendiente',
            'created_at' => $this->created_at->toIso8601String(),
            'payment' => $this->whenLoaded('payment', fn () => [
                'id' => $this->payment->id,
                'customer' => $this->payment->customer ? [
                    'id' => $this->payment->customer->id,
                    'name' => $this->payment->customer->name,
                    'codigo' => $this->payment->customer->codigo,
                    'email' => $this->payment->customer->email,
                    'dni' => $this->payment->customer->dni,
                    'ruc' => $this->payment->customer->ruc,
                    'client_type_id' => $this->payment->customer->client_type_id,
                    'state' => $this->payment->customer->state,
                    'created_at' => $this->payment->customer->created_at->toIso8601String(),
                    'updated_at' => $this->payment->customer->updated_at->toIso8601String(),
                ] : null,
                'service' => $this->payment->paymentPlanService->first() ? [
                    'id' => $this->payment->paymentPlanService->first()->id,
                    'name' => $this->payment->paymentPlanService->first()->name,
                ] : null,
                'amount' => $this->payment->amount,
                'payment_date' => $this->payment->payment_date ? $this->payment->payment_date->toIso8601String() : null,
                'payment_method' => $this->payment->payment_method,
                'reference' => $this->payment->reference,
                'status' => $this->payment->status,
            ]),
        ];
    }
}