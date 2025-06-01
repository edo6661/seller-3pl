<?php

namespace App\Request;

use Illuminate\Foundation\Http\FormRequest;

class TransferWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_user_id' => 'required|integer|exists:users,id|different:from_user_id',
            'amount' => 'required|numeric|min:1000',
            'description' => 'required|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'to_user_id.required' => 'Penerima transfer wajib dipilih.',
            'to_user_id.integer' => 'ID penerima harus berupa angka.',
            'to_user_id.exists' => 'Pengguna penerima tidak ditemukan.',
            'to_user_id.different' => 'Tidak bisa transfer ke diri sendiri.',
            'amount.required' => 'Jumlah transfer wajib diisi.',
            'amount.numeric' => 'Jumlah transfer harus berupa angka.',
            'amount.min' => 'Minimal transfer Rp 1.000.',
            'description.required' => 'Deskripsi transfer wajib diisi.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 255 karakter.'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'from_user_id' => auth()->id()
        ]);
    }
}
