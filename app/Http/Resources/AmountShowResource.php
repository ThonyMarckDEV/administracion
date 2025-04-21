<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'category_name' => $this->categories->name,
            'supplier_id' => $this->supplier_id,
            'supplier_name' => $this->suppliers->name,
            'description' => $this->description,
            'amount' => $this->amount,
            'date_init' => Carbon::parse($this->date_init)->toDateString(),
        ];
    }
}
