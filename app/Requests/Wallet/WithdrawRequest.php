<?php

namespace App\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:50000|max:50000000',
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50|regex:/^[0-9]+$/',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah penarikan wajib diisi.',
            'amount.numeric' => 'Jumlah penarikan harus berupa angka.',
            'amount.min' => 'Minimum penarikan adalah Rp 50.000.',
            'amount.max' => 'Maksimum penarikan adalah Rp 50.000.000.',
            'bank_name.required' => 'Nama bank wajib diisi.',
            'bank_name.max' => 'Nama bank maksimal 100 karakter.',
            'account_name.required' => 'Nama pemilik rekening wajib diisi.',
            'account_name.max' => 'Nama pemilik rekening maksimal 100 karakter.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'account_number.max' => 'Nomor rekening maksimal 50 karakter.',
            'account_number.regex' => 'Nomor rekening hanya boleh berisi angka.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => (float) str_replace([',', '.'], ['', ''], $this->amount)
            ]);
        }

        if ($this->has('account_number')) {
            $this->merge([
                'account_number' => preg_replace('/[^0-9]/', '', $this->account_number)
            ]);
        }
    }
}