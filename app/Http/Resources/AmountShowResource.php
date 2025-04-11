<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmountShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'description' => $this->description,
            'amount' => $this->amount,
            'date_init' => $this->date_init,
        ];
    }
}
