<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePickupStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,processing,picked_up,delivered,cancelled,failed',
            'courier_tracking_number' => 'nullable|string|max:100',
            'courier_response' => 'nullable|array',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status tidak valid.',
            'courier_tracking_number.max' => 'Nomor resi maksimal 100 karakter.',
            'notes.max' => 'Catatan maksimal 1000 karakter.'
        ];
    }
}
