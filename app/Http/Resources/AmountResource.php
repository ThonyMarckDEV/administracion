<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmountResource extends JsonResource
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
            'category' => $this->categories->name,
            'supplier' => $this->suppliers->name,
            'ruc' => $this->suppliers->ruc,
            'description' => $this->description,
            'amount' => $this->amount,
            'date_init' => Carbon::parse($this->date_init)->toDateString(),
        ];
    }
}
