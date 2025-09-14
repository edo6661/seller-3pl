<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'amount_formatted' => 'Rp ' . number_format($this->amount, 0, ',', '.'),
            'type' => $this->type->value, 
            'type_label' => ucfirst($this->type->value), 
            'status' => $this->status->value, 
            'status_label' => ucfirst($this->status->value), 
            'description' => $this->description,
            'reference_id' => $this->reference_id,
            'balance_before' => $this->balance_before,
            'balance_after' => $this->balance_after,
            'wallet' => [
                'id' => $this->wallet->id,
                'user' => [
                    'id' => $this->wallet->user->id,
                    'name' => $this->wallet->user->name,
                    'email' => $this->wallet->user->email,
                ]
            ],
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i'),
        ];
    }
}