<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PickupRequestResource extends JsonResource
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
            'pickup_code' => $this->pickup_code,
            'user_id' => $this->user_id,
            
            
            'recipient' => [
                'name' => $this->recipient_name,
                'phone' => $this->recipient_phone,
                'city' => $this->recipient_city,
                'province' => $this->recipient_province,
                'postal_code' => $this->recipient_postal_code,
                'address' => $this->recipient_address,
                'full_address' => $this->full_recipient_address,
                'latitude' => $this->recipient_latitude,
                'longitude' => $this->recipient_longitude,
            ],
            
            
            'pickup' => [
                'name' => $this->pickup_name,
                'phone' => $this->pickup_phone,
                'city' => $this->pickup_city,
                'province' => $this->pickup_province,
                'postal_code' => $this->pickup_postal_code,
                'address' => $this->pickup_address,
                'full_address' => $this->full_pickup_address,
                'latitude' => $this->pickup_latitude,
                'longitude' => $this->pickup_longitude,
                'scheduled_at' => $this->pickup_scheduled_at?->format('Y-m-d H:i:s'),
            ],
            
            
            'payment' => [
                'method' => $this->payment_method,
                'shipping_cost' => (float) $this->shipping_cost,
                'service_fee' => (float) $this->service_fee,
                'product_total' => (float) $this->product_total,
                'cod_amount' => (float) $this->cod_amount,
                'total_amount' => (float) $this->total_amount,
                'is_balance_payment' => $this->isBalancePayment(),
                'is_wallet_payment' => $this->isWalletPayment(),
            ],
            
            
            'status' => $this->status,
            'status_info' => [
                'can_be_cancelled' => $this->canBeCancelled(),
                'is_completed' => $this->isCompleted(),
                'is_failed' => $this->isFailed(),
            ],
            
            
            'courier' => [
                'service' => $this->courier_service,
                'tracking_number' => $this->courier_tracking_number,
                'response' => $this->courier_response,
            ],
            
            
            'notes' => $this->notes,
            
            
            'dates' => [
                'requested_at' => $this->requested_at?->format('Y-m-d H:i:s'),
                'picked_up_at' => $this->picked_up_at?->format('Y-m-d H:i:s'),
                'delivered_at' => $this->delivered_at?->format('Y-m-d H:i:s'),
                'cod_collected_at' => $this->cod_collected_at?->format('Y-m-d H:i:s'),
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            ],
            
            
            'items' => PickupRequestItemResource::collection($this->whenLoaded('items')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}