<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'weight_per_pcs' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'weight_per_pcs.required' => 'Berat per pcs wajib diisi.',
            'weight_per_pcs.numeric' => 'Berat per pcs harus berupa angka.',
            'weight_per_pcs.min' => 'Berat per pcs tidak boleh kurang dari 0.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh kurang dari 0.',
            'is_active.boolean' => 'Status aktif harus berupa true atau false.'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id(),
            'is_active' => $this->is_active ?? true
        ]);
    }
}
