<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawRequestResource extends JsonResource
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
            'withdrawal_code' => $this->withdrawal_code,
            'status' => $this->status,
            'status_label' => ucfirst($this->status),
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
            'admin_fee' => $this->admin_fee,
            'admin_fee_formatted' => 'Rp ' . number_format($this->admin_fee, 0, ',', '.'),
            'total_received' => $this->amount - $this->admin_fee,
            'total_received_formatted' => 'Rp ' . number_format($this->amount - $this->admin_fee, 0, ',', '.'),
            'notes' => $this->notes,
            'processed_at' => $this->processed_at ? $this->processed_at->format('d/m/Y H:i') : null,
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