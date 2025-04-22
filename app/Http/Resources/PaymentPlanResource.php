<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentPlanResource extends JsonResource
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
            'service_id' => $this->service_id,
            'service_name' => $this->service->name,
            'period_id' => $this->period_id,
            'period_name' => $this->period->name,
            'payment_type' => $this->payment_type,
            'amount' => $this->amount,
            'duration' => $this->duration,
            'state' => $this->state,
        ];
    }
}
