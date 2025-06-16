<?php


namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyerRatingResource extends JsonResource
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
            'phone_number' => $this->phone_number,
            'name' => $this->name,
            'total_orders' => (int) $this->total_orders,
            'successful_orders' => (int) $this->successful_orders,
            'cancelled_orders' => (int) $this->cancelled_orders,
            'failed_cod_orders' => (int) $this->failed_cod_orders,
            'success_rate' => (float) $this->success_rate,
            'success_rate_formatted' => number_format($this->success_rate, 2) . '%',
            'risk_level' => [
                'value' => $this->risk_level->value,
                'label' => $this->risk_level->label(),
                'color' => $this->risk_level->color(),
                'description' => $this->risk_level->description(),
            ],
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
