<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuyerRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone_number' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'total_orders' => 'required|integer|min:0',
            'successful_orders' => 'required|integer|min:0',
            'failed_cod_orders' => 'required|integer|min:0',
            'cancelled_orders' => 'required|integer|min:0',
            'success_rate' => 'required|numeric|between:0,100',
            'risk_level' => 'required|in:low,medium,high',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.max' => 'Nomor telepon maksimal 20 karakter.',
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'total_orders.required' => 'Total pesanan wajib diisi.',
            'total_orders.integer' => 'Total pesanan harus berupa angka.',
            'total_orders.min' => 'Total pesanan tidak boleh kurang dari 0.',
            'successful_orders.required' => 'Pesanan berhasil wajib diisi.',
            'successful_orders.integer' => 'Pesanan berhasil harus berupa angka.',
            'successful_orders.min' => 'Pesanan berhasil tidak boleh kurang dari 0.',
            'failed_cod_orders.required' => 'Pesanan COD gagal wajib diisi.',
            'failed_cod_orders.integer' => 'Pesanan COD gagal harus berupa angka.',
            'failed_cod_orders.min' => 'Pesanan COD gagal tidak boleh kurang dari 0.',
            'cancelled_orders.required' => 'Pesanan dibatalkan wajib diisi.',
            'cancelled_orders.integer' => 'Pesanan dibatalkan harus berupa angka.',
            'cancelled_orders.min' => 'Pesanan dibatalkan tidak boleh kurang dari 0.',
            'success_rate.required' => 'Tingkat keberhasilan wajib diisi.',
            'success_rate.numeric' => 'Tingkat keberhasilan harus berupa angka.',
            'success_rate.between' => 'Tingkat keberhasilan harus antara 0-100.',
            'risk_level.required' => 'Level risiko wajib dipilih.',
            'risk_level.in' => 'Level risiko harus salah satu dari: rendah, sedang, tinggi.',
            'notes.max' => 'Catatan maksimal 1000 karakter.'
        ];
    }
}
