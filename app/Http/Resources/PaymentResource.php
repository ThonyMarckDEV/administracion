<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => $this->customer->name,
            'service' => $this->paymentPlanService->first()->name,
            'discount' => $this->discount->percentage ?? 0,
            'amount' => $this->amount,
            'payment_date' => Carbon::parse($this->payment_date)->toDateString(),
            'payment_method' => $this->payment_method,
            'reference' => $this->reference ?? '---',
            'status' => $this->status,
        ];
    }
}
