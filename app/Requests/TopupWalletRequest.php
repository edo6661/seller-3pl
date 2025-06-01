<?php

namespace App\Request;

use Illuminate\Foundation\Http\FormRequest;

class TopupWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:10000|max:10000000',
            'description' => 'nullable|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah topup wajib diisi.',
            'amount.numeric' => 'Jumlah topup harus berupa angka.',
            'amount.min' => 'Minimal topup Rp 10.000.',
            'amount.max' => 'Maksimal topup Rp 10.000.000.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 255 karakter.'
        ];
    }
}
