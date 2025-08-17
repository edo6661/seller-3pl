<?php

namespace App\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class ManualWithdrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:50000',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|min:8|max:20|regex:/^[0-9]+$/',
            'account_name' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah penarikan harus diisi.',
            'amount.numeric' => 'Jumlah penarikan harus berupa angka.',
            'amount.min' => 'Minimum penarikan adalah Rp 50.000.',
            'bank_name.required' => 'Nama bank harus diisi.',
            'bank_name.max' => 'Nama bank maksimal 100 karakter.',
            'account_number.required' => 'Nomor rekening harus diisi.',
            'account_number.min' => 'Nomor rekening minimal 8 digit.',
            'account_number.max' => 'Nomor rekening maksimal 20 digit.',
            'account_number.regex' => 'Nomor rekening hanya boleh berisi angka.',
            'account_name.required' => 'Nama pemilik rekening harus diisi.',
            'account_name.max' => 'Nama pemilik rekening maksimal 100 karakter.',
        ];
    }
}