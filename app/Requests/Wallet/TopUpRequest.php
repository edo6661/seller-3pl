<?php

namespace App\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class TopUpRequest extends FormRequest
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
            'amount' => 'required|numeric|min:10000|max:10000000',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'string'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah top up wajib diisi.',
            'amount.numeric' => 'Jumlah top up harus berupa angka.',
            'amount.min' => 'Minimum top up adalah Rp 10.000.',
            'amount.max' => 'Maksimum top up adalah Rp 10.000.000.',
            'payment_methods.array' => 'Format metode pembayaran tidak valid.',
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
    }
}
