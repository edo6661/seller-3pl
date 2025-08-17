<?php

namespace App\Requests\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_type' => ['required', Rule::in(['general', 'shipment'])],
            'pickup_code' => 'required_if:ticket_type,shipment|nullable|string|max:50',
            'tracking_number' => 'nullable|string|max:100',
            'category' => [
                'required',
                Rule::in([
                    'delivery_issue',
                    'payment_issue',
                    'item_damage',
                    'item_lost',
                    'wrong_address',
                    'courier_service',
                    'app_technical',
                    'account_issue',
                    'other'
                ])
            ],
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // Max 2MB per file
        ];
    }

    public function messages(): array
    {
        return [
            'ticket_type.required' => 'Tipe tiket harus dipilih.',
            'pickup_code.required_if' => 'Kode pickup harus diisi untuk tiket pengiriman.',
            'category.required' => 'Kategori masalah harus dipilih.',
            'subject.required' => 'Subjek tiket harus diisi.',
            'subject.max' => 'Subjek maksimal 255 karakter.',
            'description.required' => 'Deskripsi masalah harus diisi.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
            'priority.required' => 'Prioritas harus dipilih.',
            'attachments.max' => 'Maksimal 5 file attachment.',
            'attachments.*.file' => 'Attachment harus berupa file.',
            'attachments.*.mimes' => 'Format file yang diizinkan: jpg, jpeg, png, pdf, doc, docx.',
            'attachments.*.max' => 'Ukuran file maksimal 2MB.',
        ];
    }

    protected function prepareForValidation()
    {
        // Jika general ticket, kosongkan pickup_code dan tracking_number
        if ($this->ticket_type === 'general') {
            $this->merge([
                'pickup_code' => null,
                'tracking_number' => null,
            ]);
        }
    }
}