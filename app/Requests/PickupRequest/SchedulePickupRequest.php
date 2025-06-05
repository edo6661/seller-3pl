<?php

namespace App\Requests\PickupRequest;
use Illuminate\Foundation\Http\FormRequest;

class SchedulePickupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_scheduled_at' => 'required|date|after:now'
        ];
    }

    public function messages(): array
    {
        return [
            'pickup_scheduled_at.required' => 'Jadwal pickup wajib diisi.',
            'pickup_scheduled_at.date' => 'Jadwal pickup harus berupa tanggal yang valid.',
            'pickup_scheduled_at.after' => 'Jadwal pickup harus setelah waktu sekarang.',
        ];
    }
}