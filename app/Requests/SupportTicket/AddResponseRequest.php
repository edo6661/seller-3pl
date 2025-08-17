<?php

namespace App\Requests\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

class AddResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'required|string|max:2000',
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Pesan respons harus diisi.',
            'message.max' => 'Pesan maksimal 2000 karakter.',
            'attachments.max' => 'Maksimal 3 file attachment.',
            'attachments.*.mimes' => 'Format file yang diizinkan: jpg, jpeg, png, pdf, doc, docx.',
            'attachments.*.max' => 'Ukuran file maksimal 2MB.',
        ];
    }
}
