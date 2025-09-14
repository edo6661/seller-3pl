<?php

namespace App\Requests\Profile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResubmitVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSeller();
    }

    public function rules(): array
    {
        return [
            'ktp_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'passbook_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}