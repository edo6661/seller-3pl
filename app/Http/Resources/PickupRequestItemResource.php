<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PickupRequestItemResource extends JsonResource
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
            'pickup_request_id' => $this->pickup_request_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'weight_per_pcs' => (float) $this->weight_per_pcs,
            'price_per_pcs' => (float) $this->price_per_pcs,
            'total_weight' => (float) ($this->quantity * $this->weight_per_pcs),
            'total_price' => (float) ($this->quantity * $this->price_per_pcs),
            'product' => new ProductResource($this->whenLoaded('product')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}