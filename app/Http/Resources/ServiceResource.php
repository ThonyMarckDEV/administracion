<?php
namespace App\Http\Resources;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'name' => $this->name,
            'cost' => is_numeric($this->cost) ? floatval($this->cost) : 0,
            'ini_date' => $this->ini_date,
            'state' => $this->state,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
        ];
    }
}