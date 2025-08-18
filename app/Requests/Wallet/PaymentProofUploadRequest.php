<?php

namespace App\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class PaymentProofUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_proof.required' => 'Bukti pembayaran harus diupload.',
            'payment_proof.image' => 'File harus berupa gambar.',
            'payment_proof.mimes' => 'Format file harus jpg, jpeg, atau png.',
            'payment_proof.max' => 'Ukuran file maksimal 2MB.',
        ];
    }
}