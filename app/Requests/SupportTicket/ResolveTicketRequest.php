<?php
namespace App\Requests\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

class ResolveTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'resolution' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'resolution.required' => 'Resolusi masalah harus diisi.',
            'resolution.max' => 'Resolusi maksimal 1000 karakter.',
        ];
    }
}