<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'price_formatted' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'weight_per_pcs' => number_format($this->weight_per_pcs, 1),
            'weight_per_pcs_formatted' => number_format($this->weight_per_pcs, 1) . ' kg',
            'is_active' => $this->is_active,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i'),
        ];
    }
}