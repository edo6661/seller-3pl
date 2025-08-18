<?php

namespace App\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class TopUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:10000|max:10000000',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah top up harus diisi.',
            'amount.numeric' => 'Jumlah top up harus berupa angka.',
            'amount.min' => 'Minimum top up adalah Rp 10.000.',
            'amount.max' => 'Maksimum top up adalah Rp 10.000.000.',
        ];
    }
}
