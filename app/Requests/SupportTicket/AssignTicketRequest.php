<?php
namespace App\Requests\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

class AssignTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'admin_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'admin_id.required' => 'Admin harus dipilih.',
            'admin_id.exists' => 'Admin tidak valid.',
        ];
    }
}