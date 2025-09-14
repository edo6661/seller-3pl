<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'remember' => 'boolean',
            'device-name' => 'string|max:255|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 6 karakter.',
            'remember.boolean' => 'Remember me harus berupa true atau false.',
            'device-name.string' => 'Nama perangkat harus berupa teks.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'remember' => $this->remember ?? false,
        ]);
    }
}